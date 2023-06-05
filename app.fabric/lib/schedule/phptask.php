<?php

namespace App\Fabric\Schedule;

use App\Fabric\Job;

class PhpTask extends Task
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
        parent::__construct();
    }

    public function handle()
    {
        //TODO handler errors
        $this->getLogger()->secure()->continue(function(){
            $closure = $this->getClosureData();
            $this->getLogger()->debug("[".$this->getKey()."]Start exec function `".$closure[0]."\nWith arguments: ".json_encode($closure[1])."\n");
            $callback = $this->callback;
            $result = $callback();
            $this->getLogger()->debug("[".$this->getKey()."]Done with result: ".print_r($result, true));
        });
    }

    private function getClosureData(): array
    {
        $ref  = new \ReflectionFunction($this->callback);
        $file = new \SplFileObject($ref->getFileName());
        $file->seek($ref->getStartLine()-1);
        $content = '';
        while ($file->key() < $ref->getEndLine()) {
            $content .= $file->current();
            $file->next();
        }
        return array(
            $content,
            $ref->getStaticVariables()
        );
    }

    /**
     * @throws \ReflectionException
     */
    protected function getHandleKey()
    {
        return md5(json_encode($this->getClosureData()));
    }
}