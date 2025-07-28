<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Account;
use App\Models\Category;
use App\Models\Payment;
use Carbon\Carbon;

class FinancialForecast extends Component
{
    public $accounts;
    public $categories;
    public $payments;
    public $selectedCategories = [];
    public $prospectivePayments = [];
    public $forecastMonths = 12;
    public $investmentAmount;
    public $investmentYears;
    public $investmentRate = 0.05;

    protected $listeners = ['updateForecast' => '$refresh'];

    public function mount()
    {
        $this->accounts = Account::with('payments')->get();
        $this->categories = Category::all();
        $this->payments = Payment::with('account','category')->get();
        $this->selectedCategories = $this->categories->pluck('id')->toArray();
    }

    public function toggleCategory($categoryId)
    {
        if (($key = array_search($categoryId, $this->selectedCategories)) !== false) {
            unset($this->selectedCategories[$key]);
        } else {
            $this->selectedCategories[] = $categoryId;
        }
        $this->selectedCategories = array_values($this->selectedCategories);
    }

    public function addProspectivePayment($name, $amount, $accountId, $categoryId, $frequency = 'monthly')
    {
        $this->prospectivePayments[] = [
            'name' => $name,
            'amount' => $amount,
            'account_id' => $accountId,
            'category_id' => $categoryId,
            'frequency' => $frequency,
        ];
    }

    public function getCashflowProperty()
    {
        $cashflow = [];
        $start = Carbon::now()->startOfMonth();
        for ($i = 0; $i < $this->forecastMonths; $i++) {
            $monthStart = $start->copy()->addMonths($i);
            $income = 0;
            $expense = 0;

            foreach ($this->payments as $payment) {
                if (! in_array($payment->category_id, $this->selectedCategories)) {
                    continue;
                }
                if ($payment->recurring) {
                    if (!$payment->active) continue;
                    $amount = $payment->amount;
                    if ($payment->type === 'income') {
                        $income += $amount;
                    } else {
                        $expense += $amount;
                    }
                }
            }

            foreach ($this->prospectivePayments as $prospective) {
                if (! in_array($prospective['category_id'], $this->selectedCategories)) {
                    continue;
                }
                $amount = $prospective['amount'];
                if ($prospective['frequency'] === 'monthly') {
                    // treat as monthly
                }
                $expense += $amount;
            }

            $cashflow[] = [
                'month' => $monthStart->format('Y-m'),
                'net' => $income - $expense,
                'income' => $income,
                'expense' => $expense,
            ];
        }

        return $cashflow;
    }

    public function getInvestmentTotalProperty()
    {
        if (!$this->investmentAmount || !$this->investmentYears) {
            return 0;
        }
        $monthlyRate = ($this->investmentRate) / 12;
        $n = $this->investmentYears * 12;
        $pmt = $this->investmentAmount;
        $fv = $pmt * ( (pow(1 + $monthlyRate, $n) - 1) / $monthlyRate );
        return round($fv, 2);
    }

    public function render()
    {
        return view('livewire.financial-forecast');
    }
}
