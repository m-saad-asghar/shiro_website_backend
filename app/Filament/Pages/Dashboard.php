<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Agent;
use App\Models\Developer;
use App\Models\Property;
use App\Models\SaleAgent;
use App\Models\ContactForm;
use Illuminate\Support\Carbon;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';
    protected static string $view = 'filament.pages.dashboard';

    public $cards;
    public $usersChart;
    public $salesChart;
    public $agentsSalesChart;
    public $propertiesByDeveloperChart;

    public function mount()
    {
        $this->cards = [
            ['label' => 'Users Count', 'value' => User::count(), 'color' => '#094834'], // primary (green)
            ['label' => 'Agents Count', 'value' => Agent::count(), 'color' => '#d3c294'], // secondary (gold)
            ['label' => 'Developers Count', 'value' => Developer::count(), 'color' => '#9f8151'], // gray (brown)
            ['label' => 'Properties Count', 'value' => Property::count(), 'color' => '#000000'], // dark (black)
            ['label' => 'Sales Count', 'value' => SaleAgent::count(), 'color' => '#1E8449'], // success (dark green)
            ['label' => 'Contact Messages', 'value' => ContactForm::count(), 'color' => '#A93226'], // danger (red)
        ];

        $this->usersChart = $this->getUsersChartData();
        $this->salesChart = $this->getSalesChartData();
        $this->agentsSalesChart = $this->getAgentsSalesData();
        $this->propertiesByDeveloperChart = $this->getPropertiesByDeveloperData();
    }

    protected function getUsersChartData(): array
    {
        $months = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('F'));
        $counts = $months->map(fn($m, $idx) =>
        User::whereMonth('created_at', $idx + 1)->whereYear('created_at', now()->year)->count()
        );

        return ['labels' => $months, 'data' => $counts];
    }

    protected function getSalesChartData(): array
    {
        $months = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('F'));
        $counts = $months->map(fn($m, $idx) =>
        SaleAgent::whereMonth('date', $idx + 1)->whereYear('date', now()->year)->count()
        );

        return ['labels' => $months, 'data' => $counts];
    }

    protected function getAgentsSalesData(): array
    {
        $agents = Agent::withCount('sales')->orderBy('sales_count', 'desc')->take(5)->get();

        return [
            'labels' => $agents->pluck('name'),
            'data' => $agents->pluck('sales_count'),
        ];
    }

    protected function getPropertiesByDeveloperData(): array
    {
        $developers = Developer::withCount('properties')->orderBy('properties_count', 'desc')->take(5)->get();

        return [
            'labels' => $developers->pluck('name'),
            'data' => $developers->pluck('properties_count'),
        ];
    }
}
