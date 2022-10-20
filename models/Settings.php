<?php namespace Pensoft\LinkCheck\Models;

use Model;
use File;
use DB;
use Schema;
use Pensoft\LinkCheck\Classes\Helper;

class Settings extends Model {
    use \October\Rain\Database\Traits\Validation;

    public $implement = [ 'System.Behaviors.SettingsModel' ];

    public $settingsCode = 'bombozama_linkcheck_settings';

    public $settingsFields = 'fields.yaml';

    public $rules = [ 'time' => 'required' ];

    # Render options for dropdowns on settings/fields.yaml
    public function getModelatorOptions( $keyValue = null ) {
        $models  = $out = [];
        $authors = File::directories( plugins_path() );
        foreach ( $authors as $author ) {
            foreach ( File::directories( $author ) as $plugin ) {
                $modelPath = $plugin . DIRECTORY_SEPARATOR . 'models';
                if ( ! File::exists( $modelPath ) ) {
                    continue;
                }

                foreach ( File::files( $modelPath ) as $modelFile ) {
                    # All links in the LinkCheck plugin table are broken. Skip.
                    $linkCheckPluginPath = plugins_path() . DIRECTORY_SEPARATOR . 'pensoft' . DIRECTORY_SEPARATOR . 'linkcheck';
                    if ( $plugin == $linkCheckPluginPath ) {
                        continue;
                    }

                    $models[] = Helper::getFullClassNameFromFile( (string) $modelFile );
                }
            }
        }
        foreach ( $models as $model ) {
            if ( substr( $model, - 5 ) == 'Pivot' ) {
                continue;
            }

            $object = new $model();
            foreach ( Schema::getColumnListing( $object->table ) as $column ) {
                $type = DB::connection()->getDoctrineColumn( $object->table, $column )->getType()->getName();
                if ( in_array( $type, [ 'string', 'text' ] ) ) {
                    $out[ $model . '::' . $column ] = $model . '::' . $column;
                }
            }
        }

        return $out;
    }
}
