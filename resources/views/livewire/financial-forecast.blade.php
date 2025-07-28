<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Financial Forecast</h1>

    <div class="mb-4">
        <h2 class="font-semibold mb-2">Toggle Categories</h2>
        @foreach($categories as $category)
            <label class="inline-flex items-center mr-4">
                <input type="checkbox" wire:click="toggleCategory({{ $category->id }})" {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                <span class="ml-2">{{ $category->name }}</span>
            </label>
        @endforeach
    </div>

    <!-- Investment calculator -->
    <div class="mb-4">
        <h2 class="font-semibold mb-2">Investment Calculator</h2>
        <div class="flex flex-wrap gap-2">
            <input type="number" step="0.01" wire:model="investmentAmount" placeholder="Monthly amount" class="border px-2 py-1 rounded">
            <input type="number" wire:model="investmentYears" placeholder="Years" class="border px-2 py-1 rounded">
            <input type="number" step="0.01" wire:model="investmentRate" placeholder="Annual rate (e.g. 0.05)" class="border px-2 py-1 rounded">
        </div>
        <div class="mt-2">Estimated future value: <strong>{{ $this->investmentTotal }}</strong></div>
    </div>

    <div class="mt-6">
        <canvas id="cashflowChart" width="400" height="200"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('livewire:load', () => {
    const ctx = document.getElementById('cashflowChart').getContext('2d');
    let chart;

    function renderChart(data) {
        const labels = data.map(item => item.month);
        const netData = data.map(item => item.net);
        if (chart) chart.destroy();
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Net Cashflow',
                    data: netData,
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    renderChart(@this.get('cashflow'));

    window.addEventListener('updateForecast', () => {
        renderChart(@this.get('cashflow'));
    });
});
</script>
@endpush
