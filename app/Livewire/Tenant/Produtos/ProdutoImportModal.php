<?php

namespace App\Livewire\Produtos;

use Livewire\Component;
use App\Models\Tenant\Produtos;
use App\Models\Tenant\Categorias;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Symfony\Contracts\Service\Attribute\Required;

class ProdutoImportModal extends Component
{
    use Actions;
    use WithFileUploads;

    public $produtoImportModal = false;

    #[Required('nullable|sometimes|file|max:1024')]
    public $arquivo;

    public $resetar_produtos = false;

    #[Locked]
    public $dados;

    #[Locked]
    public $produtos = [];
    #[Locked]
    public $categorias = [];
    #[Locked]
    public $unidades_medidas = [];

    #[\Livewire\Attributes\On('import')]
    public function create(): void
    {
        $this->resetValidation();

        $this->reset();

        $this->js('$openModal("produtoImportModal")');
    }

    public function updatedArquivo($value)
    {
        if($value) {
            // $arquivo_url = $value->store('uploads', 'public', ['disk' => 'public']);

            // $validated['name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // $validated['file_mimetype'] = $file->getClientMimeType();
            $validated['extension'] = pathinfo($value->getClientOriginalName(), PATHINFO_EXTENSION);
            // $validated['full_name'] = $file->getClientOriginalName();

            if($validated['extension'] == "xlsx") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }else if($validated['extension'] == "xls") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }

            $spreadsheet = $reader->load($value->getRealPath());

            $this->dados = $spreadsheet->getSheet(0)->toArray();

            $this->removeFile();

            $start = false;

            $deleted_at = date('Y-m-d h:i:s');

            $categorias = Categorias::select('id', 'titulo', 'slug', 'empresas_id')->whereEmpresasId(auth()->user()->empresas_id)->orderBy('titulo')->get()->toArray();

            foreach ($categorias as $key => $value) {
                $categorias[$value['titulo']] = $value;
                unset($categorias[$key]);
            }

            $unidades_medidas = [];
            // $unidades_medidas = \App\Model\UnidadesMedidas::whereEmpresasId(auth()->user()->empresas_id)->orderBy('sigla')->pluck('id', 'sigla')->toArray();

            // foreach ($unidades_medidas as $key => $value) {
            //     $unidades_medidas[$key] = strtoupper(trim($value));
            // }

            foreach ($this->dados as $key => $value) {
                if(!$start) {
                    if(trim($value[0]) == "Código Interno Sistema") $start = true;
                    continue;
                }else{
                    if(is_null($value[1])) continue;
                }

                foreach ($value as $key_2 => $value_2) {
                    $value[$key_2] = trim($value_2);
                    if(empty($value_2) && $value_2 != "0") $value[$key_2] = null;
                }

                $categoria      = $value[2];
                $unidade_medida = $value[10];

                if(!empty($categoria)) {
                    if(array_key_exists($categoria, $categorias)) {
                        // $categoria = $categorias[$categoria];
                    }else{
                        $categorias[$categoria] = [
                            'id'            => null,
                            'titulo'        => $categoria,
                            'slug'          => Str::slug($categoria, '-'),
                            'empresas_id'   => auth()->user()->empresas_id
                        ];
    
                        // $categoria = \App\Models\Categorias::create([
                        //     'titulo'        => $categoria,
                        //     'slug'          => Str::slug($categoria, '-'),
                        //     'empresas_id'   => auth()->user()->empresas_id
                        // ]);
    
                        // $categorias[$categoria->titulo] = $categoria->id;
                        // $categoria                      = $categoria->id;
                    }
                }

                // if(array_key_exists($unidade_medida, $unidades_medidas)) {
                //     $unidade_medida = $unidades_medidas[$unidade_medida];
                // }else{
                //     $unidade_medida = \App\Model\UnidadesMedidas::create([
                //         'descricao' => $unidade_medida,
                //         'slug' => Str::slug($unidade_medida, '-'),
                //         'empresas_id' => $empresa->id
                //     ]);

                //     $unidades_medidas[$unidade_medida->sigla] = $unidade_medida->id;
                //     $unidade_medida = $unidade_medida->id;
                // }

                $titulo = str_replace('  ', ' ', $value[1]);
                $titulo = str_replace([' Ml', ' ml'], 'ml', $titulo);
                $titulo = mb_convert_encoding($titulo, 'UTF-8');
    
                $descricao = str_replace('  ', ' ', $value[13]);
                $descricao = str_replace([' Ml', ' ml'], 'ml', $descricao);
                $descricao = mb_convert_encoding($descricao, 'UTF-8');

                if($this->rcEmoji($descricao, 'c')) $descricao = trim($this->rcEmoji($descricao, 'r'));
                $descricao = str_replace(["'", '"'], '', $descricao);

                $linha = array(
                    'titulo'                => ucwords($titulo),
                    'slug'                  => Str::slug($titulo, '-'),
                    'preco_varejo'          => floatval(str_replace(',', '.', $value[3])),
                    'preco_promocao'        => floatval(str_replace(',', '.', $value[4])),
                    'promocao_inicio'       => $value[5],
                    'promocao_fim'          => $value[6],
                    'destaque'              => $value[7],
                    'estoque_atual'         => $value[8],

                    'unidade_medida'        => $unidade_medida,
                    'codigo_externo'        => $value[11],
                    'somente_mesa'          => $value[12],

                    'descricao'             => $descricao,
                    'codigo_barras_1'       => $value[14],
                    'empresas_id'           => auth()->user()->empresas_id,
                    // 'codigo_barras_1'    => $value[14],
                    // 'codigo_barras_2'    => $value[15],
                    // 'codigo_barras_3'    => $value[16],
                    // 'preco_atacado'      => floatval(str_replace(',', '.', $value[17])),
                    'categoria'             => Str::slug($categoria, '-'),
                );

                $linha['deleted_at'] = $value[9] != 1 ? $deleted_at : null;

                /*
                if($this->resetar_produtos) {
                    $produto_encontrado = auth()->user()->empresa->produtos()->withTrashed()
                    ->where(function (Builder $query) use ($value) {
                        $query->where('descricao', $value[1]);
                        if($value[0]) $query->orWhere('id', '=', $value[0]);
                        // if($value[12]) $query->orWhere('codigo_barras_1', '=', $value[12]);
                        // if($value[13]) $query->orWhere('codigo_barras_2', '=', $value[13]);
                        // if($value[14]) $query->orWhere('codigo_barras_3', '=', $value[14]);
                        return $query;
                    })
                    ->first();

                    if($produto_encontrado) {
                        if($request->get('acao') == 4) {
                            unset($produto_encontrado);
                            continue;
                        }

                        $linha_update = [];

                        if($request->get('acao') == 1) {
                            $linha_update = $linha;
                        }
                        elseif($request->get('acao') == 2) {
                            $linha_update['preco_varejo']      = floatval(str_replace(',', '.', $value[3]));
                            $linha_update['preco_promocao']    = floatval(str_replace(',', '.', $value[4]));
                            $linha_update['promocao_inicio']   = $value[5];
                            $linha_update['promocao_fim']      = $value[6];
                            // $linha_update['preco_atacado']     = floatval(str_replace(',', '.', $value[4]));
                        }
                        elseif($request->get('acao') == 3) {
                            // 'trib_cst'              => $value[1],
                            // 'trib_icms'             => $value[1],
                            // 'trib_csosn'            => $value[1],
                            // 'trib_cfop_de'          => $value[1],
                            // 'trib_cfop_fe'          => $value[1],
                            // 'trib_ncm'              => $value[1],
                            // 'trib_cest'             => $value[1],
                            // 'trib_origem_produto'   => $value[1],
                        }

                        $produto_encontrado->update($linha_update);

                        if($request->get('acao') == 1) $produto_encontrado->categorias()->sync(['categorias_id' => $categoria]);

                        unset($linha_update);
                        unset($produto_encontrado);
                        continue;
                    }

                    if($request->get('acao') != 4) {
                        MyLog::create(['assunto' => $empresa->nome_fantasia.' realizou uma importação de '.(count($s)-1).' produtos', 'username' => Auth::user()->username, 'id_user', Auth::user()->id]);

                        return redirect()->back()->with('message', 'Importação realizada com sucesso');
                    }
                }
                */

                $produtos[] = $linha;

                // $produto = Produtos::create($linha);
                // $produto->categorias()->sync(['categorias_id' => $categoria]);
            }

            $this->categorias = $categorias;
            $this->produtos = $produtos;
        }
    }

    public function save($params=null)
    {
        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Importar estes novos produtos e categorias?',
                'acceptLabel' => 'Sim, importe',
                'method'      => 'save',
                'params'      => 'Import',
            ]);
            return;
        }

        // $validated['slug'] = Str::slug($this->form->titulo);
        // $validated['empresas_id'] = auth()->user()->empresas_id;

        DB::beginTransaction();

        try {

            foreach($this->categorias as $key => $categoria) {
                $result = Categorias::create($categoria);

                $this->categorias[$key]['id'] = $result->id;
            }

            $categorias = collect($this->categorias);

            foreach($this->produtos as $key => $produto) {
                $result = Produtos::create($produto);

                $categoria = $categorias->firstWhere('slug', $produto['categoria']);
                if($categoria) $result->categorias()->sync(['categorias_id' => $categoria['id']]);
            }

            $this->reset('produtoImportModal');
    
            $this->notification([
                'title'       => 'Dados importados!',
                'description' => 'Importação finalizada com sucesso.',
                'icon'        => 'success'
            ]);

            $this->dispatch('pg:eventRefresh-default');

            DB::commit();

        } catch (\Throwable $th) {
            throw $th;

            DB::rollBack();
    
            $this->notification([
                'title'       => 'Falha na importação!',
                'description' => 'Não foi possivel finalizar a importação.',
                'icon'        => 'error'
            ]);
        }
    }

    #[\Livewire\Attributes\On('onCloseProdutoImportModal')]
    public function removeFile()
    {
        if($this->arquivo) {
            $this->arquivo->delete();
            $this->reset('arquivo');
        }
    }

    public function render()
    {
        return view('livewire.produtos.produto-import-modal');
    }

    private function rcEmoji($string, $action = null) {
		if($action == 'r') {
			/* Remove o Emoji da String */

			// Match Enclosed Alphanumeric Supplement
		    $regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
		    $clear_string = preg_replace($regex_alphanumeric, '', $string);

		    // Match Miscellaneous Symbols and Pictographs
		    $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
		    $clear_string = preg_replace($regex_symbols, '', $clear_string);

		    // Match Emoticons
		    $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
		    $clear_string = preg_replace($regex_emoticons, '', $clear_string);

		    // Match Transport And Map Symbols
		    $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
		    $clear_string = preg_replace($regex_transport, '', $clear_string);
		    
		    // Match Supplemental Symbols and Pictographs
		    $regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
		    $clear_string = preg_replace($regex_supplemental, '', $clear_string);

		    // Match Miscellaneous Symbols
		    $regex_misc = '/[\x{2600}-\x{26FF}]/u';
		    $clear_string = preg_replace($regex_misc, '', $clear_string);

		    // Match Dingbats
		    $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
		    $clear_string = preg_replace($regex_dingbats, '', $clear_string);

			return $clear_string;

		}elseif($action == 'c') {
			/* Verifica se tem Emoji na String */
			$length = mb_strlen($string, 'UTF-8');
			for ($i = 0; $i < $length; $i++) {
				$char = mb_substr($string, $i, 1, 'UTF-8');
				if (strlen($char) > 3) return true;
			}
			return false;
		}
	}
}
