<div class="flex flex-wrap sm:flex-nowrap sm:flex-row justify-evenly sm:justify-center gap-2 sm:gap-40 p-2 dark:text-white">

    <div class="grid grid-cols-2 sm:grid-cols-1 gap-2">
        <div class="text-center col-span-2 sm:col-auto">
            <span class="text-2xl sm:text-3xl font-bold">Valores</span>
        </div>
        <div class="mx-auto min-w-[150px] sm:min-w-[200px]">
            <span>Dinheiro</span>
            <div class="dark:bg-secondary-800 dark:text-secondary-400 border border-secondary-300 dark:border-secondary-600 form-input block py-0 w-full rounded-md shadow-sm text-end">
                <span class="sm:text-2xl">{{ number_format($pagamentos['dinheiro'] ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
        <div class="mx-auto min-w-[150px] sm:min-w-[200px]">
            <span>Ticket</span>
            <div class="dark:bg-secondary-800 dark:text-secondary-400 border border-secondary-300 dark:border-secondary-600 form-input block py-0 w-full rounded-md shadow-sm text-end">
                <span class="sm:text-2xl">{{ number_format($pagamentos['ticket'] ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
        <div class="mx-auto min-w-[150px] sm:min-w-[200px]">
            <span>Cartão Débito</span>
            <div class="dark:bg-secondary-800 dark:text-secondary-400 border border-secondary-300 dark:border-secondary-600 form-input block py-0 w-full rounded-md shadow-sm text-end">
                <span class="sm:text-2xl">{{ number_format($pagamentos['cartao_debito'] ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
        <div class="mx-auto min-w-[150px] sm:min-w-[200px]">
            <span>Cartão Crédito</span>
            <div class="dark:bg-secondary-800 dark:text-secondary-400 border border-secondary-300 dark:border-secondary-600 form-input block py-0 w-full rounded-md shadow-sm text-end">
                <span class="sm:text-2xl">{{ number_format($pagamentos['cartao_credito'] ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
        <div class="sm:mx-auto col-span-2 sm:col-auto sm:min-w-[200px]">
            <span>Total do Caixa</span>
            <div class="dark:bg-indigo-800 dark:text-indigo-400 border border-secondary-300 dark:border-indigo-700 form-input block py-0 w-full rounded-md shadow-sm text-end bg-indigo-500 text-white">
                <span class="sm:text-2xl">{{ number_format($pagamentos->sum() ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-1 gap-2">
        <div class="grow text-center col-span-2 sm:col-auto">
            <span class="text-2xl sm:text-3xl font-bold">Informações</span>
        </div>
        <div class="mx-auto min-w-[150px] sm:min-w-[200px]">
            <span>Abertura</span>
            <div class="dark:bg-secondary-800 dark:text-secondary-400 border border-secondary-300 dark:border-secondary-600 form-input block py-0 w-full rounded-md shadow-sm text-end">
                <span class="sm:text-2xl">{{ number_format($caixa->entradas->firstWhere('motivo', 'e')->valor ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
        <div class="mx-auto min-w-[150px] sm:min-w-[200px]">
            <span>Descontos</span>
            <div class="dark:bg-secondary-800 dark:text-secondary-400 border border-secondary-300 dark:border-secondary-600 form-input block py-0 w-full rounded-md shadow-sm text-end">
                <span class="sm:text-2xl">{{ number_format($caixa->vendas->sum('desconto') ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
        <div class="mx-auto min-w-[150px] sm:min-w-[200px]">
            <span>Sangrias</span>
            <span class="float-right">Total: {{ $caixa->sangrias->count() }}</span>
            <div class="dark:bg-secondary-800 dark:text-secondary-400 border border-secondary-300 dark:border-secondary-600 form-input block py-0 w-full rounded-md shadow-sm text-end">
                <span class="sm:text-2xl">{{ number_format($caixa->sangria_total ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
        <div class="mx-auto min-w-[150px] sm:min-w-[200px]">
            <span>Entradas</span>
            <span class="float-right">Total: {{ $caixa->entradas->count() }}</span>
            <div class="dark:bg-secondary-800 dark:text-secondary-400 border border-secondary-300 dark:border-secondary-600 form-input block py-0 w-full rounded-md shadow-sm text-end">
                <span class="sm:text-2xl">{{ number_format($caixa->entrada_total ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
        <div class="sm:mx-auto col-span-2 sm:col-auto sm:min-w-[200px]">
            <span>Total da Gaveta</span>
            <div class="dark:bg-warning-800 dark:text-warning-400 border border-secondary-300 dark:border-warning-700 form-input block py-0 w-full rounded-md shadow-sm text-end bg-warning-400 text-white">
                <span class="sm:text-2xl">{{ number_format($this->gaveta_total ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>