<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\PatientVisit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Apply tenant filtering to a query.
     * - Super admin: no filter (sees all data).
     * - System admin with client_id: filters to their client.
     * - System admin without client_id (legacy): sees rows with NULL tenant_id.
     */
    private function scope(Builder $query, string $table = ''): Builder
    {
        $user   = auth()->user();
        $col    = $table ? "{$table}.tenant_id" : 'tenant_id';

        if ($user->isSuper()) {
            return $query;
        }

        return $user->client_id
            ? $query->where($col, $user->client_id)
            : $query->whereNull($col);
    }

    public function index()
    {
        $user       = auth()->user();
        $now        = now();
        $monthStart = $now->copy()->startOfMonth();
        $weekStart  = $now->copy()->subDays(6)->startOfDay();
        $day30Ago   = $now->copy()->subDays(29)->startOfDay();

        // ── KPI cards ──────────────────────────────────────────────
        $totalPatients  = $this->scope(Patient::query())->count();
        $activeCases    = $this->scope(PatientVisit::query())->whereNull('discharge_date')->count();
        $visitsThisMonth = $this->scope(PatientVisit::query())->where('visit_date', '>=', $monthStart)->count();
        $visitsThisWeek  = $this->scope(PatientVisit::query())->where('visit_date', '>=', $weekStart)->count();

        // Revenue this month: sum of line totals from invoice_items
        $revenueThisMonth = $this->revenueSum('invoices.invoice_date', '>=', $monthStart);
        $totalPaid        = $this->scope(Invoice::query())->sum('money_paid');
        $totalBilled      = $this->revenueSum(null, null, null); // all time
        $outstanding      = max(0, $totalBilled - $totalPaid);

        // ── Visits per day (last 30 days) — chart data ─────────────
        $visitRows = $this->scope(PatientVisit::query())
            ->where('visit_date', '>=', $day30Ago)
            ->get(['visit_date'])
            ->groupBy(fn($v) => $v->visit_date->format('Y-m-d'))
            ->map->count();

        $visitLabels = [];
        $visitData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->format('Y-m-d');
            $visitLabels[] = $now->copy()->subDays($i)->format('M d');
            $visitData[]   = $visitRows->get($d, 0);
        }

        // ── Revenue per day (last 30 days) — chart data ────────────
        $revenueRows = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.invoice_date', '>=', $day30Ago->toDateString())
            ->when(!$user->isSuper() && $user->client_id,
                fn($q) => $q->where('invoices.tenant_id', $user->client_id))
            ->when(!$user->isSuper() && !$user->client_id,
                fn($q) => $q->whereNull('invoices.tenant_id'))
            ->selectRaw("
                DATE(invoices.invoice_date) as d,
                COALESCE(SUM(invoice_items.unit_price * invoice_items.quantity
                    * (1 - invoice_items.discount_pct / 100.0)), 0) as rev
            ")
            ->groupBy('d')
            ->pluck('rev', 'd');

        $revenueData = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->format('Y-m-d');
            $revenueData[] = round((float) ($revenueRows->get($d, 0)), 2);
        }

        // ── OPD vs IPD (this month) ─────────────────────────────────
        $visitTypes = $this->scope(PatientVisit::query())
            ->where('visit_date', '>=', $monthStart)
            ->whereIn('visit_type', ['OPD', 'IPD'])
            ->selectRaw('visit_type, COUNT(*) as cnt')
            ->groupBy('visit_type')
            ->pluck('cnt', 'visit_type');

        $opdCount = (int) $visitTypes->get('OPD', 0);
        $ipdCount = (int) $visitTypes->get('IPD', 0);

        // ── Recent visits ───────────────────────────────────────────
        $recentVisits = $this->scope(PatientVisit::query())
            ->with('patient')
            ->orderByDesc('visit_date')
            ->limit(8)
            ->get();

        // ── Recent invoices ─────────────────────────────────────────
        $recentInvoices = $this->scope(Invoice::query())
            ->with(['patient', 'paymentType', 'items'])
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        // ── Per-client summary (super_admin only) ───────────────────
        $clientSummary = null;
        if ($user->isSuper()) {
            $clientSummary = Client::withCount('users')
                ->withCount(['patients'])
                ->withCount([
                    'patients as active_cases_count' => fn($q) =>
                        $q->whereHas('visits', fn($vq) => $vq->whereNull('discharge_date')),
                ])
                ->orderBy('name')
                ->get();
        }

        return view('analytics.index', compact(
            'totalPatients', 'activeCases', 'visitsThisMonth', 'visitsThisWeek',
            'revenueThisMonth', 'totalPaid', 'totalBilled', 'outstanding',
            'visitLabels', 'visitData',
            'revenueData',
            'opdCount', 'ipdCount',
            'recentVisits', 'recentInvoices',
            'clientSummary'
        ));
    }

    /**
     * Sum line totals from invoice_items, with optional date filter on invoices.
     */
    private function revenueSum(?string $dateColumn, ?string $operator, mixed $value): float
    {
        $user = auth()->user();

        $q = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id');

        if ($dateColumn && $operator && $value !== null) {
            $q->where($dateColumn, $operator, $value instanceof \Carbon\Carbon ? $value->toDateString() : $value);
        }

        if (!$user->isSuper()) {
            $user->client_id
                ? $q->where('invoices.tenant_id', $user->client_id)
                : $q->whereNull('invoices.tenant_id');
        }

        return (float) $q->selectRaw(
            'COALESCE(SUM(invoice_items.unit_price * invoice_items.quantity * (1 - invoice_items.discount_pct / 100.0)), 0) as total'
        )->value('total');
    }
}
