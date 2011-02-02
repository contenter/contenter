<?php
final class FinalView_Doctrine
{
    private static $_path;

    public static function getPath()
    {
        if ( ! self::$_path) {
            self::$_path = realpath(dirname(__FILE__));
        }

        return self::$_path;
    }

    public static function init($config)
    {
        $manager = Doctrine_Manager::getInstance();

        $manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);
        $manager->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);

        $manager->setAttribute(
            Doctrine::ATTR_MODEL_LOADING,
            Doctrine::MODEL_LOADING_CONSERVATIVE
        );

        $manager->setAttribute(Doctrine::ATTR_TABLE_CLASS, 'FinalView_Doctrine_Table');

        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        if(array_key_exists('models_path', $config)) {
            Doctrine::loadModels($config['models_path']);
        }

        if(array_key_exists('connection_string', $config)) {
            $manager->openConnection($config['connection_string'])->setCharset('UTF8');
        }

        FinalView_Doctrine::registerHydratorsPath(
            FinalView_Doctrine::getPath() . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'Hydrator',
            'FinalView_Doctrine_Hydrator'
        );
    }

    public static function registerHydratorsPath($path, $prefix)
    {
        $manager = Doctrine_Manager::getInstance();

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $file) {
            $e = explode('.', $file->getFileName());

            if (end($e) == 'php') {
                $name = $e[0];

                $manager->registerHydrator(
                    $name,
                    $prefix . '_' . $name
                );
            }
        }
    }


    /**
     * Generates models from database to temporary location then uses those models to generate a yaml schema file.
     * This should probably be fixed. We should write something to generate a yaml schema file directly from the database.
     *
     * @param string $yamlPath Path to write oyur yaml schema file to
     * @param array  $options Array of options
     * @return void
     */
    public static function generateYamlFromDbTable($yamlPath, $filename, $models, array $databases = array(), array $options = array())
    {
        $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tmp_doctrine_models';

        $options['generateBaseClasses'] = isset($options['generateBaseClasses']) ? $options['generateBaseClasses']:false;
        $result = Doctrine::generateModelsFromDb($directory, $databases, $options);

        if ( empty($result) && ! is_dir($directory)) {
            throw new Doctrine_Exception('No models generated from your databases');
        }

        $export = new FinalView_Doctrine_Export_Schema();

        $result = $export->exportSchema($yamlPath, $filename, $directory, $models);

        Doctrine_Lib::removeDirectories($directory);

        return $result;
    }

    /**
     * Get the connection object for a table by the actual table name
     * FIXME: I think this method is flawed because a individual connections could have the same table name
     *
     * @param string $tableName
     * @return Doctrine_Connection
     */
    public static function getConnectionByTableName($tableName)
    {
        $loadedModelsFiles = Doctrine::getLoadedModelFiles();

        foreach ($loadedModelsFiles as $model => $modelPath) {
            if (substr($model, 0, 4) === 'Base') {
                $baseModels[] = $model;
                continue;
            }
            $customModels[] = $model;
        }

        $models = array();

        foreach ($customModels as $modelName) {
            if (in_array('Base'.$modelName, $baseModels)) {
                $models[] = $modelName;
            }
        }

        $models = Doctrine::filterInvalidModels($models);

        foreach ($models as $name) {
            $table = Doctrine::getTable($name);

            if ($table->getTableName() == $tableName) {
               return $table->getConnection();
            }
        }

        return Doctrine_Manager::connection();
    }
}
