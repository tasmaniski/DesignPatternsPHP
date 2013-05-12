<?php

/*
 * DesignPatternPHP
 */

namespace DesignPatterns\ChainOfResponsibilities;

/**
 * Handler is a generic handler in the chain of responsibilities
 * 
 * Yes you could have a lighter CoR with simpler handler but if you want your CoR to
 * be extendable and decoupled, it's a better idea to do things like that in real
 * situations. Usually, a CoR is meant to be changed everytime and evolves, that's
 * why we slice the workflow in little bits of code.
 */
abstract class Handler
{

    private $successor = null;

    /**
     * Append a responsibility to the end of chain
     * 
     * A prepend method could be done with the same spirit
     * 
     * You could also send the successor in the contructor but in PHP it is a
     * bad idea because you have to remove the type-hint of the parameter because
     * the last handler has a null successor.
     * 
     * And if you override the contructor, that Handler can no longer have a
     * successor. One solution is to provide a NullObject (see pattern).
     * It is more preferable to keep the constructor "free" to inject services
     * you need with the DiC of symfony2 for example.
     */
    final public function append(Handler $handler)
    {
        if (is_null($this->successor)) {
            $this->successor = $handler;
        } else {
            $this->successor->append($handler);
        }
    }

    /**
     * Handle the request. 
     * 
     * This approach by using a template method pattern ensures you that 
     * each subclass will not forget to call the successor. Beside, the returned
     * boolean value indicates you if the request have been processed or not.
     */
    final public function handle(Request $req)
    {
        $processed = $this->processing($req);
        if (!$processed) {
            // the request has not been processed by this handler => see the next
            if (!is_null($this->successor)) {
                $processed = $this->successor->handle($req);
            }
        }

        return $processed;
    }

    /**
     * Each concrete handler has to implement the processing of the request
     * 
     * @return bool true if the request has been processed
     */
    abstract protected function processing(Request $req);
}