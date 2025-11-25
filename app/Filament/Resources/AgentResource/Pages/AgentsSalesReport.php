<?php

namespace App\Filament\Resources\AgentResource\Pages;

use App\Models\Agent;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Carbon;

class AgentsSalesReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.resources.agent-resource.pages.agents-sales-report';

    protected static ?string $title = 'Agents Sales Report';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Agents Sales Report';

    public function getTableQuery(): Builder
    {
        return Agent::query();
    }

    public function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')->label('Agent')->sortable()->searchable(),

            Tables\Columns\TextColumn::make('sales_count')
                ->label('Properties Sold')
                ->getStateUsing(function ($record) {
                    $filters = $this->tableFilterState;

                    $from = $filters['date']['from'] ?? null;
                    $to = $filters['date']['to'] ?? null;
                    $propertyName = $filters['property_name'] ?? null;

                    return $record->sales()
                        ->when($from, fn($q) => $q->whereDate('date', '>=', Carbon::parse($from)))
                        ->when($to, fn($q) => $q->whereDate('date', '<=', Carbon::parse($to)))
                        ->when($propertyName, fn($q) =>
                        $q->whereHas('property', fn($qq) => $qq->where('name', 'like', "%$propertyName%"))
                        )
                        ->count();
                })
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
                    TextInput::make('property_name')->label('Property Name'),
                ]),
        ];
    }
}
