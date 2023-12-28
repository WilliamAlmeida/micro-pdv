@php $version = $GLOBALS['version'] ?? 0 @endphp

<style type="text/css">
	.container-ticket {
		max-width: 320px;
		/*background-color: lightyellow;*/
		/*padding: 10px;*/
		font-size: 14px !important;
	}
	.container-ticket .table {
		width: 100%;
		font-size: 14px !important;
	}

	.container-ticket .text-center { text-align: center; }
	.container-ticket .text-left { text-align: left; }
	.container-ticket .text-right { text-align: right; }
	.container-ticket .text-uppercase { text-transform: uppercase; }
	.container-ticket .text-lowercase { text-transform: lowercase; }
	.container-ticket .my-0 { margin-top: 0; margin-bottom: 0; }
	.container-ticket .mt-0 { margin-top: 0; }
	.container-ticket .mb-0 { margin-bottom: 0; }

	.pull-right { float: right; }

	.border-bottom { border-bottom: solid 1px black; }
	.border-bottom-d { border-bottom: dotted 1px black; }

	.border-top { border-top: solid 1px black; }
	.border-top-d { border-top: dotted 1px black; }

	.mb-0 { margin-bottom: 0; }
	.mb-1 { margin-bottom: 4px; }

	.mt-0 { margin-top: 0; }
	.mt-1 { margin-top: 4px; }

	.my-0 { margin-top: 0; margin-bottom: 0; }
	.my-1 { margin-top: 4; margin-bottom: 4; }

	.title-1 {
		font-size: 23px !important;
		font-weight: 900;
	}
	.title-2 {
		font-size: 20px !important;
	}
	.total {
		font-size: 20px;
		font-weight: bolder;
	}
	.site {
		-moz-transform: scale(1.003, 1);
		-webkit-transform: scale(1.003, 1);
		-ms-transform: scale(1.003, 1);
	}
	.img {
		height: 120px;
		margin-top: 8px;
		margin-bottom: 8px;
	}
	.img-2 {
		height: 60px;
	}

	.code {
		background-color: black;
		color: white;
		border-radius: 100%;
		/*font-size: 10px;*/
		padding: 0 2px;
		min-width: 10px;
		display: inline-block;
		text-align: center;
	}

	.text-transform-1, .text-transform- { text-transform: capitalize; !important; }
	.text-transform-2 { text-transform: uppercase !important; }

	.ff-traditionellsans { font-family: 'TraditionellSans', 'Arial', sans-serif !important; }

	.text-break {
		word-break: break-word !important;
		overflow-wrap: break-word !important;
	}

	.badge {
		display: inline-block;
		padding: 0.25em 0.4em;
		font-size: 90%;
		font-weight: 700;
		line-height: 1;
		text-align: center;
		white-space: nowrap;
		vertical-align: baseline;
		border-radius: 0.25rem;
		transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
	}

	.badge-success {
		color: #fff;
		background-color: #28a745;
	}
	.font-weight-bold { font-weight: 700 !important; }
</style>

<div class="container-ticket ff-traditionellsans">
	<p class="text-center text-uppercase mb-0">
		<strong>{{ $empresa->nome_fantasia }}</strong>
	</p>

	<table class="table text-left text-uppercase">
		<tbody>
			<tr>
			@if($empresa->cnpj)
				<td>
					<strong>CNPJ:</strong> {{ $empresa->cnpj }}
				</td>
				<td>
					<strong>IE:</strong> {{ $empresa->inscricao_estadual }}
				</td>
			@else
				<td>
					<strong>CPF:</strong> {{ $empresa->cpf }}
				</td>
			@endif
			</tr>

			<tr>
				<td colspan="2">
					{{ $empresa->end_logradouro }}, {{ $empresa->end_numero }} - {{ $empresa->end_bairro }}, 
					{{ $empresa->end_cidade }}/{{ $empresa->estado->uf ?? 'S/UF' }}
				</td>
			</tr>

			@if($empresa->whatsapp)
			<tr>
				<td colspan="2">
					Tel.: {{ $empresa->whatsapp }}
				</td>
			</tr>
			@endif
		</tbody>
	</table>

	@if($dados->caixa->user)
	<hr class="my-1">

	<table class="table text-left text-uppercase">
		<tbody>
			<tr>
				<td>
					Operador:
					{{ $dados->caixa->user->name }}
				</td>
				<td class="text-right">
					Operação:
					<strong>{{ $dados->tipo == 's' ? 'Sangria' : 'Entrada' }}</strong>
				</td>
			</tr>
			<tr>
				<td>
					Código da {{ $dados->tipo == 's' ? 'Sangria' : 'Entrada' }}:
					{{ $dados->id }}
				</td>
				<td class="text-right">
					{{ date('d.m.Y') }} às {{ date('H:i') }}
				</td>
			</tr>
		</tbody>
	</table>
	@endif

	<div class="mb-0 border-bottom"></div>

	<p class="text-uppercase my-1">
		<strong>Motivo:</strong> {{ $dados->motivo ?? 'Não Informado' }}
	</p>

	<div class="mb-0 border-bottom"></div>

	<table class="table text-left mb-0 border-bottom">
		<tbody>
			<tr class="total">
				<td>VALOR:</td>
				<td class="text-right">R$ @money($dados->valor)</td>
			</tr>
		</tbody>
	</table>

	<p class="text-center mb-0">
		<!-- <img src="{{--asset('images/ibox_ticket.png')--}}" class="img-2" alt="logo pdv" /> -->
		<!-- <br/> -->
		<span class="site"><small>www.wilpdv.com.br</small></span>
	</p>
</div>

<script type="text/javascript" defer>
	document.addEventListener("DOMContentLoaded", function(event) {
		var ua = navigator.userAgent.toLowerCase();
		var isAndroid = ua.indexOf("android") > -1;
		if(isAndroid) {
			window.print();
		}
	});
</script>