<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ManifestController extends Controller
{
    public function manifest()
    {
        return Storage::disk('public')->get('manifest.json');
    }

    public function generate()
    {
        $sizes = $this->arrayIconSplash();

		$dir = "icons/";

        // $now = now()->timestamp;

		// foreach ($sizes as $key => $dimensions) {
		// 	foreach ($dimensions as $key_2 => $value) {
		// 		if($key == "icon") {
		// 			foreach (config('laravelpwa.manifest.icons') as $key_3 => $value_2) {
		// 				if($value_2['sizes'] == $value[0].'x'.$value[1]) {
		// 					$conf = 'laravelpwa.manifest.icons.'.($key_3).'.src';
		// 				}
		// 			}
		// 		}else{
		// 			$conf = 'laravelpwa.manifest.splash.'.$value[0].'x'.$value[1];
		// 		}

		// 		$current_path	= config($conf);

		// 		if($current_path) {
		// 			$new_path		= str_replace("icons/", $dir, $current_path);
		// 			if($value[0] == 144) {
		// 				$new_path		= str_replace(".png", '-'.$now.'.png', $new_path);
		// 			}else{
		// 				$new_path		= str_replace(".jpg", '-'.$now.'.jpg', $new_path);
		// 			}

		// 			Config::set($conf, $new_path);
		// 		}
		// 	}
		// }

		Config::set('laravelpwa.name', config('app.name'));
		Config::set('laravelpwa.manifest.name', config('app.name'));
		Config::set('laravelpwa.manifest.short_name', config('app.name'));
		Config::set('laravelpwa.manifest.start_url', route('home').'?m=1');
		Config::set('laravelpwa.manifest.scope', '/');
		$config = Config::get('laravelpwa.manifest');

		unset($config['shortcuts']);

		$dir = '/';

		Storage::disk('public')->delete($dir.'manifest.json');
		Storage::disk('public')->put($dir.'manifest.json', str_replace("\\", "", json_encode($config, JSON_UNESCAPED_UNICODE)), 'public');

		return Storage::disk('public')->get($dir.'manifest.json');
    }

	public function arrayIconSplash() {
		$array = array(
			'icon' => array(
				// 1 => array(48,48),
				2 => array(72,72),
				3 => array(96,96),
				4 => array(128,128),
				5 => array(144,144),
				6 => array(152,152),
				7 => array(192,192),
				8 => array(196,196),
				9 => array(384,384),
				10 => array(512,512)
			),
			'splash' => array(
				1 => array(1125,2436),
				2 => array(750,1334),
				3 => array(1242,2208),
				4 => array(640,1136),
				5 => array(1536,2048),
				7 => array(1668,2224),
				8 => array(2048,2732)
			)
		);

		return $array;
	}
}
