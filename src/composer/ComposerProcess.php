<?php
namespace johnitvn\workbench\composer;

/**
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class ComposerProcess {

    public $error;
    protected $command;
    protected $workingDir;

    /**
     * Constructor
     *
     * @param mixed $workingDir
     * @return Process
     */
    public function __construct($workingDir = null) {
        $this->setWorkingDir($workingDir);
    }

    /**
     * Runs a composer CLI command and captures the output.
     *
     * @param string|array $params The composer params
     * @param array $output The returned output
     * @param string|null $workingDir The working directory
     * @return boolean Whether the exit code is 0
     */
    public function capture($params, &$output, $workingDir = null) {
        return $this->processWork($params, $workingDir, $output, true);
    }

    /**
     * Runs a composer CLI command.
     *
     * @param string|array $params The composer params
     * @param string|null $workingDir The working directory
     * @return boolean Whether the exit code is 0
     */
    public function run($params, $workingDir = null) {
        return $this->processWork($params, $workingDir, $dummy, false);
    }

    /**
     * Check if composer is installed
     *
     * @return boolean Whether composer is installed
     */
    public function installed() {
        return $this->run('--version');
    }

    /**
     * Returns the command for calling the composer CLI. If composer.phar is in
     * the current project directory this will be 'php "full/path/to/composer.phar"',
     * otherwise it will be 'composer'.
     *
     * @return string The command
     */
    public function getCommand() {
        if (!$this->command) {
            if ($composerPhar = $this->getPhar(false)) {
                $this->command = 'php ' . escapeshellarg($composerPhar);
            } else {
                $this->command = 'composer';
            }
        }

        return $this->command;
    }

    /**
     * Searches for composer.phar and returns its full path
     *
     * @return string|null The full path to composer.phar
     */
    public function getComposerPhar() {
        return $this->getPhar(true);
    }

    /**
     * Sets workingDir for process calls. Can be unset by passing null or an empty string
     *
     * @param mixed $path
     */
    public function setWorkingDir($path) {
        $this->workingDir = $path && is_string($path) ? $path : null;
    }

    /**
     * Creates a new Package
     *
     * @param array $values The basic package properties
     * @return Package
     */
    public function packageCreate($values, $filename = null) {
        $package = new ComposerPackage();
        $package->create($values);
        $package->filename = $this->packageGetName($filename);
        return $package;
    }

    /**
     * Creates a Package from an existing composer.json file
     *
     * @param string|null $filename Package filename or empty to use composer.json
     * @return Package
     */
    public function packageOpen($filename) {
        $package = new ComposerPackage();
        $filename = $this->packageGetName($filename);
        $package->open($filename);
        return $package;
    }

    /**
     * Runs composer install on the package
     *
     * @param mixed $package A Package, filename or empty for composer.json
     * @param string|array $params
     * @return boolean
     */
    public function packageInstall($package, $params = array()) {
        return $this->packageWork($package, $params, true);
    }

    /**
     * Runs composer unpdate on the package
     *
     * @param mixed $package A Package, filename or empty for composer.json
     * @param string|array $params
     * @return boolean
     */
    public function packageUpdate($package, $params = array()) {
        return $this->packageWork($package, $params, false);
    }

    /**
     * Searches for composer.phar and returns its full path
     *
     * @param boolean $global Whether to search outside the current project directory
     * @return string|null The full path to composer.phar
     */
    protected function getPhar($global) {
        $composerPhar = null;
        $path = __DIR__;

        while ($pos = strrpos($path, DIRECTORY_SEPARATOR . 'vendor')) {
            $path = substr($path, 0, $pos + 1) . 'composer.phar';

            if (file_exists($path)) {
                $composerPhar = $path;
                break;
            }
        }

        if (!$composerPhar && $global) {
            $envPaths = explode(PATH_SEPARATOR, getenv('path'));

            foreach ($envPaths as $path) {
                $path .= '/composer.phar';
                if (file_exists($path)) {
                    $composerPhar = $path;
                    break;
                }
            }

            if (!$composerPhar) {

                foreach ($envPaths as $path) {
                    $path .= '/composer';
                    if (file_exists($path) && is_file($path)) {
                        $composerPhar = $path;
                        break;
                    }
                }
            }

            if (!$composerPhar) {
                $composerPhar = stream_resolve_include_path('composer.phar');
            }

            if (!$composerPhar) {
                $composerPhar = stream_resolve_include_path('composer');
            }
        }

        return $composerPhar ? strtr($composerPhar, '\\', '/') : null;
    }

    /**
     * Escapes individual arguments if passed an array
     *
     * @param string|array $params
     */
    protected function getParams($params) {
        if (is_array($params)) {
            $parts = array();

            foreach (array_map('trim', $params) as $param) {
                if ($param) {
                    $parts[] = escapeshellarg($param);
                }
            }

            $params = implode(' ', $parts);
        }

        return is_string($params) ? $params : '';
    }

    /**
     * Runs a Composer CLI command from capture or run.
     *
     * @param string|array $params The composer params
     * @param string|null $workingDir The working directory
     * @param array $output The returned output, if required
     * @param boolean $capture Whether to capture the output
     * @return boolean Whether the exit code is 0
     */
    protected function processWork($params, $workingDir, &$output, $capture) {
        $cwd = $this->changeWorkingDirectory($workingDir);
        $command = $this->getCommand() . ' ' . $this->getParams($params);

        if ($capture) {
            $output = array();
            exec($command, $output, $exitCode);
        } else {
            passthru($command, $exitCode);
        }

        if ($cwd) {
            chdir($cwd);
        }

        return $exitCode === 0;
    }

    /**
     * Changes the working directory for the current processWork call, if necessary.
     *
     * @param string|null $directory
     */
    protected function changeWorkingDirectory($directory) {
        $result = null;

        if ($directory = $directory ? : $this->workingDir) {
            $result = getcwd();
            chdir($directory);
        }

        return $result;
    }

    /**
     * Returns the package name. If filename is empty uses the working directory
     *
     * @param mixed $filename
     */
    protected function packageGetName($filename) {
        if (!$filename) {
            $path = $this->workingDir ? : getcwd();
            $filename = $path . '/composer.json';
        }

        return strtr($filename, '\\', '/');
    }

    /**
     * Runs install or update on the package
     *
     * @param mixed $package
     * @param string|array $params
     * @param boolean $install
     * @return boolean
     */
    protected function packageWork($package, $params, $install) {
        $mode = $install ? 'install' : 'update';

        if (!($package instanceof Package)) {
            $package = $this->packageOpen($package);
        }

        if (!$error = $package->error) {
            if (!$package->filename) {
                $error = 'Missing filename';
            } elseif (!file_exists($package->filename)) {
                $error = 'Cannot find file: ' . $package->filename;
            }
        }

        if ($error) {
            echo sprintf('Package %s failed. %s.', $mode, $error);
            echo PHP_EOL, PHP_EOL;

            return false;
        }

        $params = (array) $params;
        array_unshift($params, $mode);

        return $this->run($params, dirname($package->filename));
    }

}
