<?php

/**
 * Description of uploadFile
 *
 * @author juanma
 */
class uploadFile {

    /**
     * @var string Nombre completo del archivo original
     */
    private $full_name; 
    /**
     * @var string Tipo mime del archivo original
     */
    private $type;
    /**
     * @var string Ruta temporal del archivo subido
     */
    private $tmp_name;
    /**
     * @var int Cógido de error
     */
    private $error;
    /**
     * @var int Tamaño del archivo
     */
    private $size;
    /**
     * @var string Nombre del archivo validado para subir (sin extensión)
     */
    private $name;
    /**
     * @var string Extensión
     */
    private $ext;
    /**
     * @var int Tamaño máximo permitido por la clase
     */
    private $max_size;
    /**
     * @var string Ruta definitiva donde se subirá el archivo.
     */
    private $destination;
    /**
     * @var string Mensage de error actual.
     */
    private $error_message;
    private $img_array = array(3 => "jpg", 1 => "png", 2 => "gif", 4 => "svg");
    private $doc_array = array(3 => "txt", 1 => "pdf", 2 => "xls", 4 => "doc");
    /**
     * Constantes de la clase directorios por defecto de subida según el tipo de archivo
     */
    const IMG_DIR = './img/';
    const DOC_DIR = './docs/';
    const UPLOAD_GEN_DIR = './uploads/';
    /**
     * Constructor de la clase
     * @param array $file Datos del archivo (requerido)
     * @param String $dest Ruta de subida (opcional)
     * @param int $max_size Tamaño máximo del archivo (opcional)
     */
    function __construct($file, $dest = null, $max_size = 999999) {
        $this->full_name = $file['name'];
        $this->type = $file['type'];
        $this->tmp_name = $file['tmp_name'];
        $this->size = $file['size'];
        $this->max_size = $max_size;
        $this->name = $this->generateName($file['name']);
        $this->ext = $this->generateExt();        
        $this->error = $this->generateErrorCode($file['error']);   
        $this->destination = $this->generateDestination($dest);
        $this->error_message = $this->generateErrorMessage();        
    }
    /**
     * Genera un nombre válido para el archivo
     * @param String $name Nombre del archivo
     * @return string Nombre válido 
     */
    private function generateName($name) {
        $p = strripos($name, ".");
        if (strlen($name) > 100) {
            return str_replace(" ", '_', substr($name, 0, 95) . '_' . strlen($name));
        }
        return str_replace(" ", '_', substr($name, 0, $p));
    }
    /**
     * Obtiene la extensión del archivo
     * @return string Extensión del archivo
     */
    private function generateExt() {
        $p = strripos($this->full_name, ".");
        return strtolower(substr($this->full_name, $p + 1));
    }
    /**
     * Valida el archivo y genera el código de error correspondiente
     * @param int $error Código de error
     * @return int Código de error
     */
    private function generateErrorCode($error) {
        if ($error != 0) {
            return $error;
        }
        if ($this->size > $this->max_size) {
            return 101;
        }
        if ($this->ext == "php") {
            return 102;
        } else {
            return UPLOAD_ERR_OK;
        }
    }
    /**
     * Genera el mensage de error a partir del código
     * @return string Mensage de error
     */
    private function generateErrorMessage() {
        switch ($this->error) {
            case UPLOAD_ERR_OK: return "Todo ok";
            case UPLOAD_ERR_INI_SIZE: return " El fichero subido excede la directiva upload_max_filesize de php.ini";
            case UPLOAD_ERR_FORM_SIZE: return "El fichero subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML";
            case UPLOAD_ERR_PARTIAL: return " El fichero fue sólo parcialmente subido.";
            case UPLOAD_ERR_NO_FILE: return "No se subió ningún fichero.";
            case UPLOAD_ERR_NO_TMP_DIR: return "Falta la carpeta temporal.";
            case UPLOAD_ERR_CANT_WRITE: return "No se pudo escribir el fichero en el disco.";
            case UPLOAD_ERR_EXTENSION: return "Una extensión de PHP detuvo la subida de ficheros.";
            case 101: return "Ha superado el tamaño máximo establecido por la clase";
            case 102: return "Tipo de archivo no permitido";
            case 103: return "Archivo corrupto n ha sido subido por POST";
            case 104: return "La ruta especificada ('" . $this->destination . "') no existe.";
            default: return "Error de subida desconocido";
        }
    }
    /**
     * Genera la ruta de destino si entra un valor nulo o valida la ruta de ntrada en caso contrario
     * @param string $dest Ruta de destino
     * @return string Ruta de destino válida
     */
    private function generateDestination($dest) {        
        if ($dest === null) {
            if (array_search($this->ext, $img_array) >= 1) {
                return self::IMG_DIR;
            }
            if (array_search($this->ext, $doc_array) >= 1) {
                return self::DOC_DIR;
            }
            return self::UPLOAD_GEN_DIR;
        }
        if(is_dir($dest)===true){
            return $dest;
        }else{
            $this->error=$this->generateErrorCode(104);
            return $dest;
        }           
    }
    /**
     * Sube el archivo de forma definitiva al servidor validando el origen del archivo y los errores existentes
     * @param boolean $overwrite True: Sobresscrive, false: asigna un nuevo nombre 
     * @param string $newName Nombre del archivo a subir
     * @param string $destination Ruta de subida del archivo
     * @return boolean true si correcto, false en caso contrario
     */
    public function upload($overwrite = false, $newName = null, $destination = null) {
        if ($destination !== null) {
            $this->setDestination(trim($destination));
        }
        if ($newName !== null) {
            $this->name = $this->generateName($newName);
        }
        $url = $this->destination . $this->name . '.' . $this->ext;
        if (is_uploaded_file($this->tmp_name)) {
            if ($overwrite && $this->error === UPLOAD_ERR_OK) {
                return move_uploaded_file($this->tmp_name, $url);
            }
            if ($this->error === UPLOAD_ERR_OK) {
                return move_uploaded_file($this->tmp_name, $this->renameIfExists($this->name));
            } else {
                return false;
            }
        } else {
            $this->error = 104;
            return false;
        }
    }
    /**
     * Genera un nuevo nombre para el arvhivo si existe en destino
     * @param string $n nombre a verificar
     * @return string Ruta completa del archivo válido a subir
     */
    private function renameIfExists($n) {
        $i=1;
        $newName = $n; 
        while (file_exists($this->destination . $n . '.' . $this->ext)){
            $newName = $n . $i;
            $i++;
        }
        return $this->destination . $newName . '.' . $this->ext;
    }
    /**
     * Método estático para subir multiples ficheros a la vez
     * @param Array de ficheros $files
     * @param boolean $overwrite
     * @param string $destination
     */
    public static function MultiUpload($files, $overwrite=true, $destination=null){
        foreach ($files as $file){
            $upFile = new uploadFile($file, $destination);
            $upFile->upload($overwrite);
        }
    }
    /**
     * Metodo steatico para subir un archivo
     * @param array $file Datos del archivo (requerido)
     * @param String $dest Ruta de subida (opcional)
     * @param int $max_size Tamaño máximo del archivo (opcional)
     * @return boolean | string True si todo correcto o el mensaje de error en caso contrario
     */
    public static function loadFile($file, $dest = null, $max_size = null){
        $new = new uploadFile($file, $dest, $max_size);
        return $new->upload();
    }
    /*
     *  getters y setters
     */
    function getFull_name() {
        return $this->full_name;
    }

    function getType() {
        return $this->type;
    }

    function getTmp_name() {
        return $this->tmp_name;
    }

    function getError() {
        return $this->error;
    }

    function getSize() {
        return $this->size;
    }

    function getName() {
        return $this->name;
    }

    function getExt() {
        return $this->ext;
    }

    function getMax_size() {
        return $this->max_size;
    }

    function getDestination() {
        return $this->destination;
    }

    function getError_message() {
        return $this->error_message;
    }

    function setMax_size($max_size) {
        $this->max_size = $max_size;
    }
    
    function setDestination($dest){
        if(is_dir($dest)===true){
            $this->destination = $dest;
        }else{
            $this->error = 104;
            $this->destination = self::UPLOAD_GEN_DIR;
        } 
    }
    /*
     * toString
     */
    public function __toString() {
        $str = "<h5>Archivo:</h5><br/>";
        foreach ($this as $key => $value) {
            $str.=$key . " => " . $value . "<br/>";
        }
        return $str;
    }

}
