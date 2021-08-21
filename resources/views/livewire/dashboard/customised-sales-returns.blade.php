<div class="bg-white pt-6 mt-2 shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 sm:px-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Sales Returns</h2>
    </div>
    <div class="overflow-hidden">
        <canvas class="p-10 " id="chartBarReturns"></canvas>
    </div>
<!-- Chart bar -->
    <script>

        var labels = "{{$label_plucked}}";
        labels = JSON.parse(labels.replace(/&quot;/g, '"'));
        var sale_refund ="{{collect($data)->pluck('return_total')}}";
        sale_refund = JSON.parse(sale_refund.replace(/&quot;/g,'"'));
        const dataReturns = {
            labels: labels,
            datasets: [
                {
                    label: 'Sale Return',
                    data:sale_refund,
                    borderColor: '#d9534f',
                    backgroundColor: '#d9534f',
                },
            ]
        };

        const configReturn = {
            type: 'bar',
            data: dataReturns,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 16,
                                weight: 'Bold',
                            }
                        }
                    },
                    title: {
                        display: false,
                        text: 'Bar Chart'
                    },

                }
            },
        };


        var chartBar = new Chart(
            document.getElementById('chartBarReturns'),
            configReturn
        );
    </script>
</div>
