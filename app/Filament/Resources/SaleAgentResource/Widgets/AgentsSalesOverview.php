<?php

namespace App\Filament\Resources\SaleAgentResource\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Builder;

class AgentsSalesOverview extends BaseWidget
{
    protected static ?int $sort = 1; // يظهر بأعلى الصفحة
    protected int | string | array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Agent::query()
                    ->withCount(['sales as properties_sold_count' => function ($q) {
                        // يمكن تخصيص فلترة التاريخ هنا لاحقاً
                    }])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Agent Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('properties_sold_count')->label('Properties Sold')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Tables\Filters\Components\DatePicker::make('from')->label('From'),
                        Tables\Filters\Components\DatePicker::make('to')->label('To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['from']) || !empty($data['to'])) {
                            $query->withCount(['sales as properties_sold_count' => function ($q) use ($data) {
                                if (!empty($data['from'])) {
                                    $q->where('date', '>=', $data['from']);
                                }
                                if (!empty($data['to'])) {
                                    $q->where('date', '<=', $data['to']);
                                }
                            }]);
                        }
                        return $query;
                    }),
            ])
            ->defaultSort('properties_sold_count', 'desc'); // رتب افتراضياً حسب عدد المبيعات
    }
}
