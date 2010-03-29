<?php
final class FinalView_Doctrine
{
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
     * Migrate database to specified $to version. Migrates from current to latest if you do not specify.
     *
     * @param string $migrationsPath Path to migrations directory which contains your migration classes
     * @param string $to Version you wish to migrate to.
     * @return bool true
     * @throws new Doctrine_Migration_Exception
     */
    public static function migrate($migrationsPath, $to = null)
    {
        $migration = new FinalView_Doctrine_Migration($migrationsPath);

        return $migration->migrate($to);
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