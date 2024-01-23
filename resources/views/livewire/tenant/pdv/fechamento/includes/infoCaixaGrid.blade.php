<div class="grid bg-gray-300 dark:bg-gray-800 bg-gradient-to-l from-lime-400 dark:from-indigo-600 dark:text-white">
    <span class="text-2xl sm:text-3xl text-center font-bold">Fechamento do Caixa</span>

    <div class="flex flex-wrap sm:flex-row justify-evenly py-2 px-2 gap-2">
        <div class="self-center">
            <span class="text-base"><strong>Operador:</strong> {{ auth()->user()->name }}</span>
        </div>
        <div class="self-center">
            <span class="text-base"><strong>Quant. Vendas:</strong> {{ $caixa->vendas->count() }}</span>
        </div>
        <div class="self-center">
            <span class="text-base"><strong>Abertura do Caixa:</strong> {{ \Carbon\Carbon::parse($caixa->created_at)->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($caixa->created_at)->format('H:i') }}</span>
        </div>
    </div>
</div>