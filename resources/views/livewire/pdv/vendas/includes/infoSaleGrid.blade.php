<div class="grid bg-gray-300 dark:bg-gray-800 bg-gradient-to-l from-lime-400 dark:from-indigo-600 dark:text-white">
    <span class="text-2xl sm:text-3xl text-center font-bold">Total do Caixa</span>

    <div class="flex flex-wrap sm:flex-row justify-around text-center pb-4 px-2">
        <div class="self-center">
            <span class="text-base sm:text-2xl"><strong>Dinheiro</strong><br/>R$ @money($caixa->pagamentos['dinheiro'] ?? 0)</span>
        </div>
        <div class="self-center">
            <span class="text-base sm:text-2xl"><strong>Ticket</strong><br/>R$ @money($caixa->pagamentos['ticket'] ?? 0)</span>
        </div>
        <div class="self-center">
            <span class="text-base sm:text-2xl"><strong>Débito</strong><br/>R$ @money($caixa->pagamentos['cartao_debito'] ?? 0)</span>
        </div>
        <div class="self-center">
            <span class="text-base sm:text-2xl"><strong>Crédito</strong><br/>R$ @money($caixa->pagamentos['cartao_credito'] ?? 0)</span>
        </div>
        <div class="self-center">
            <span class="text-base sm:text-2xl"><strong>Total Geral</strong><br/>R$ @money($caixa->pagamentos->sum() ?? 0)</span>
        </div>
    </div>
</div>