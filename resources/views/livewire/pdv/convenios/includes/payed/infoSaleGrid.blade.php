<div class="grid bg-gray-300 dark:bg-gray-800 bg-gradient-to-l from-lime-400 dark:from-indigo-600 dark:text-white">
    <span class="text-2xl sm:text-3xl text-center font-bold">Convênio</span>

    <div class="flex flex-wrap sm:flex-row justify-around text-center pb-4 px-2">
        <div class="self-center">
            <span class="text-base sm:text-2xl"><strong>Nº Recebimentos</strong><br/>{{ count($recebimentos_convenio) }}</span>
        </div>
        <div class="self-center">
            <span class="text-base sm:text-2xl"><strong>Total Recebido</strong><br/>R$ @money($recebimentos_convenio ? $recebimentos_convenio->sum('valor_total') - $recebimentos_convenio->sum('desconto') : 0)</span>
        </div>
    </div>
</div>