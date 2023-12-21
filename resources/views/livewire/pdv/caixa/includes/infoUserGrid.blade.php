<div class="grid grid-cols-2 bg-gray-300 dark:bg-gray-800">
    <div class="font-bold py-4 px-2 my-auto">
        <div class="flex">
            <img src="https://ibox.delivery/uploads/adm/empresas/111/empresa-logo-111iZJw.png" class="h-[80px] w-[80px] mr-2 hidden sm:flex" />
            <div class="self-center dark:text-white">
                Operador: {{ $caixa->user->name ?: 'Desconhecido' }}
                <br/>
                Caixa aberto em: {{ Carbon\Carbon::parse($caixa->created_at)->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
    <div class="flex flex-row-reverse font-bold text-end bg-gradient-to-l from-lime-400 dark:from-indigo-600 dark:text-white py-4 pl-2 pr-4">
        <div class="self-center italic">
            <span class="text-2xl sm:text-3xl">Valor Total<br/>R$ {{ number_format($caixa->venda?->valor_total, 2, ',', '.') }}</span>
        </div>
    </div>
</div>