<?php
// This class was automatically generated by build task
// You should not change it manually as it will be overwritten on next build
// @codingStandardsIgnoreFile

namespace Calculation;

use \Codeception\Maybe;
use Codeception\Module\Filesystem;
use Calculation\Codeception\Module\TestHelper;

/**
 * Inherited methods
 * @method void execute($callable)
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void offsetGet($offset)
 * @method void offsetSet($offset, $value)
 * @method void offsetExists($offset)
 * @method void offsetUnset($offset)
*/

class TestGuy extends \Codeception\AbstractGuy
{

    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Enters a directory In local filesystem.
     * Project root directory is used by default
     *
     * @param $path
     * @see Codeception\Module\Filesystem::amInPath()
     * @return \Codeception\Maybe
     */
    public function amInPath($path) {
        $this->scenario->addStep(new \Codeception\Step\Condition('amInPath', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Opens a file and stores it's content.
     *
     * Usage:
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->seeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $filename
     * @see Codeception\Module\Filesystem::openFile()
     * @return \Codeception\Maybe
     */
    public function openFile($filename) {
        $this->scenario->addStep(new \Codeception\Step\Action('openFile', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Deletes a file
     *
     * ``` php
     * <?php
     * $I->deleteFile('composer.lock');
     * ?>
     * ```
     *
     * @param $filename
     * @see Codeception\Module\Filesystem::deleteFile()
     * @return \Codeception\Maybe
     */
    public function deleteFile($filename) {
        $this->scenario->addStep(new \Codeception\Step\Action('deleteFile', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Deletes directory with all subdirectories
     *
     * ``` php
     * <?php
     * $I->deleteDir('vendor');
     * ?>
     * ```
     *
     * @param $dirname
     * @see Codeception\Module\Filesystem::deleteDir()
     * @return \Codeception\Maybe
     */
    public function deleteDir($dirname) {
        $this->scenario->addStep(new \Codeception\Step\Action('deleteDir', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Copies directory with all contents
     *
     * ``` php
     * <?php
     * $I->copyDir('vendor','old_vendor');
     * ?>
     * ```
     *
     * @param $src
     * @param $dst
     * @see Codeception\Module\Filesystem::copyDir()
     * @return \Codeception\Maybe
     */
    public function copyDir($src, $dst) {
        $this->scenario->addStep(new \Codeception\Step\Action('copyDir', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Checks If opened file has `text` in it.
     *
     * Usage:
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->seeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $text
    * Conditional Assertion: Test won't be stopped on fail
     * @see Codeception\Module\Filesystem::seeInThisFile()
     * @return \Codeception\Maybe
     */
    public function canSeeInThisFile($text) {
        $this->scenario->addStep(new \Codeception\Step\ConditionalAssertion('seeInThisFile', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }
    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Checks If opened file has `text` in it.
     *
     * Usage:
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->seeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $text
     * @see Codeception\Module\Filesystem::seeInThisFile()
     * @return \Codeception\Maybe
     */
    public function seeInThisFile($text) {
        $this->scenario->addStep(new \Codeception\Step\Assertion('seeInThisFile', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Checks the strict matching of file contents.
     * Unlike `seeInThisFile` will fail if file has something more then expected lines.
     * Better to use with HEREDOC strings.
     * Matching is done after removing "\r" chars from file content.
     *
     * ``` php
     * <?php
     * $I->openFile('process.pid');
     * $I->seeFileContentsEqual('3192');
     * ?>
     * ```
     *
     * @param $text
    * Conditional Assertion: Test won't be stopped on fail
     * @see Codeception\Module\Filesystem::seeFileContentsEqual()
     * @return \Codeception\Maybe
     */
    public function canSeeFileContentsEqual($text) {
        $this->scenario->addStep(new \Codeception\Step\ConditionalAssertion('seeFileContentsEqual', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }
    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Checks the strict matching of file contents.
     * Unlike `seeInThisFile` will fail if file has something more then expected lines.
     * Better to use with HEREDOC strings.
     * Matching is done after removing "\r" chars from file content.
     *
     * ``` php
     * <?php
     * $I->openFile('process.pid');
     * $I->seeFileContentsEqual('3192');
     * ?>
     * ```
     *
     * @param $text
     * @see Codeception\Module\Filesystem::seeFileContentsEqual()
     * @return \Codeception\Maybe
     */
    public function seeFileContentsEqual($text) {
        $this->scenario->addStep(new \Codeception\Step\Assertion('seeFileContentsEqual', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Checks If opened file doesn't contain `text` in it
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->dontSeeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $text
    * Conditional Assertion: Test won't be stopped on fail
     * @see Codeception\Module\Filesystem::dontSeeInThisFile()
     * @return \Codeception\Maybe
     */
    public function cantSeeInThisFile($text) {
        $this->scenario->addStep(new \Codeception\Step\ConditionalAssertion('dontSeeInThisFile', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }
    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Checks If opened file doesn't contain `text` in it
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->dontSeeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $text
     * @see Codeception\Module\Filesystem::dontSeeInThisFile()
     * @return \Codeception\Maybe
     */
    public function dontSeeInThisFile($text) {
        $this->scenario->addStep(new \Codeception\Step\Assertion('dontSeeInThisFile', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Deletes a file
     * @see Codeception\Module\Filesystem::deleteThisFile()
     * @return \Codeception\Maybe
     */
    public function deleteThisFile() {
        $this->scenario->addStep(new \Codeception\Step\Action('deleteThisFile', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Checks if file exists in path.
     * Opens a file when it's exists
     *
     * ``` php
     * <?php
     * $I->seeFileFound('UserModel.php','app/models');
     * ?>
     * ```
     *
     * @param $filename
     * @param string $path
    * Conditional Assertion: Test won't be stopped on fail
     * @see Codeception\Module\Filesystem::seeFileFound()
     * @return \Codeception\Maybe
     */
    public function canSeeFileFound($filename, $path = null) {
        $this->scenario->addStep(new \Codeception\Step\ConditionalAssertion('seeFileFound', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }
    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Checks if file exists in path.
     * Opens a file when it's exists
     *
     * ``` php
     * <?php
     * $I->seeFileFound('UserModel.php','app/models');
     * ?>
     * ```
     *
     * @param $filename
     * @param string $path
     * @see Codeception\Module\Filesystem::seeFileFound()
     * @return \Codeception\Maybe
     */
    public function seeFileFound($filename, $path = null) {
        $this->scenario->addStep(new \Codeception\Step\Assertion('seeFileFound', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     * Erases directory contents
     *
     * ``` php
     * <?php
     * $I->cleanDir('logs');
     * ?>
     * ```
     *
     * @param $dirname
     * @see Codeception\Module\Filesystem::cleanDir()
     * @return \Codeception\Maybe
     */
    public function cleanDir($dirname) {
        $this->scenario->addStep(new \Codeception\Step\Action('cleanDir', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }


    /**
     * This method is generated.
     * Documentation taken from corresponding module.
     * ----------------------------------------------
     *
     *
     * @see Codeception\Module::getName()
     * @return \Codeception\Maybe
     */
    public function getName() {
        $this->scenario->addStep(new \Codeception\Step\Action('getName', func_get_args()));
        if ($this->scenario->running()) {
            $result = $this->scenario->runStep();
            return new Maybe($result);
        }
        return new Maybe();
    }
}

