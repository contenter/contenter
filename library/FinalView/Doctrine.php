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
}