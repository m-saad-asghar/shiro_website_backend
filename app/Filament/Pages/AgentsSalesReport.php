<?php

namespace App\Filament\Pages;

use App\Models\SaleAgent;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Support\Carbon;

class AgentsSalesReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.pages.agents-sales-report';

    protected static ?string $title = 'Agent Property Sales Report';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Agent Property Sales Report';

    public function getTableQuery(): Builder
    {
        $from = $this->getTableFilterState('date')['from'] ?? null;
        $to = $this->getTableFilterState('date')['to'] ?? null;

        $propertyNameFilter = $this->resolveFilterValue($this->getTableFilterState('property_name'));
        $agentNameFilter = $this->resolveFilterValue($this->getTableFilterState('agent_name'));
        $globalSearch = $this->getTableSearch();

        $query = SaleAgent::query()
            ->selectRaw('agent_id, property_id, COUNT(*) as sales_count')
            ->when($from, fn($q) => $q->whereDate('date', '>=', Carbon::parse($from)))
            ->when($to, fn($q) => $q->whereDate('date', '<=', Carbon::parse($to)))
            ->groupBy('agent_id', 'property_id')
            ->with([
                'agent' => fn($q) => $q->withTrashed(),
                'property' => fn($q) => $q->withTrashed(),
            ])
            ->when($propertyNameFilter, function ($q, $filter) {
                $q->whereHas('property', fn($qq) => $qq->where('title->' . app()->getLocale(), 'like', "%$filter%"));
            })
            ->when($agentNameFilter, function ($q, $filter) {
                $q->whereHas('agent', fn($qq) => $qq->where('name', 'like', "%$filter%"));
            })
            ->when($globalSearch, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->whereHas('agent', fn($qq) => $qq->where('name', 'like', "%$search%"))
                          ->orWhereHas('property', fn($qq) => $qq->where('title->' . app()->getLocale(), 'like', "%$search%"));
                });
            });

        // Default sorting by sales_count
        $query->orderBy('sales_count', 'desc');

        $trashed = $this->getTableFilterState('trashed') ?? 'without';

        if ($trashed === 'with') {
            $query->withTrashed();
        } elseif ($trashed === 'only') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        return $query;
    }

    private function resolveFilterValue($filterState)
    {
        if (is_array($filterState)) {
            foreach ($filterState as $value) {
                if (is_string($value)) {
                    return $value;
                }
            }
            return null;
        }
        return $filterState;
    }

    public function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('agent_name')
                ->label('Agent')
                ->getStateUsing(fn ($record) => $record->agent?->name ?? 'N/A'),

            Tables\Columns\TextColumn::make('property_title')
                ->label('Property Title')
                ->getStateUsing(fn ($record) => $record->property?->getTranslation('title', app()->getLocale()) ?? 'N/A'),

            Tables\Columns\TextColumn::make('converted_price')
                ->label('Price')
                ->getStateUsing(fn ($record) => $record->property?->converted_price ?? '-'),

            Tables\Columns\TextColumn::make('sales_count')
                ->label('Sales Count')
                ->sortable(),
        ];
    }

    public function getTableFilters(): array
    {
        return [
            Filter::make('date')
                ->form([
                    DatePicker::make('from')->label('From Date'),
                    DatePicker::make('to')->label('To Date'),
                ]),

            Filter::make('property_name')
                ->form([
                    TextInput::make('property_name')->label('Property Title'),
                ]),

            Filter::make('agent_name')
                ->form([
                    TextInput::make('agent_name')->label('Agent Name'),
                ]),

            TrashedFilter::make(),
        ];
    }

    public function getTableRecordKey($record): string
    {
        return $record->agent_id . '_' . $record->property_id;
    }
}
