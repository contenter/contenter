<?php
/*
 * $Id: Schema.php 1838 2007-06-26 00:58:21Z nicobn $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Doctrine_Export_Schema
 * 
 * Used for exporting a schema to a yaml file
 *
 * @package     Doctrine
 * @subpackage  Export
 * @link        www.phpdoctrine.org
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version     $Revision: 1838 $
 * @author      Nicolas Bérard-Nault <nicobn@gmail.com>
 * @author      Jonathan H. Wage <jwage@mac.com>
 */
class FinalView_Doctrine_Export_Schema extends Doctrine_Export_Schema
{    

    /**
     * exportSchema
     *
     * @param  string $schema 
     * @param  string $directory 
     * @return string $string of data in the specified format
     * @return void
     */
    public function exportSchema($schema, $filename = 'schema.yml', $directory = null, $models = array())
    {
        $array = $this->buildSchema($directory, $models);
        
        if (is_dir($schema)) {
          $schema = $schema . DIRECTORY_SEPARATOR . $filename;
        }
        
        return Doctrine_Parser::dump($array, 'yml', $schema);
    }
}