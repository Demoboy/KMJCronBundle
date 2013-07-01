<?php

namespace KMJ\UpdateBundle\Service;

/**
 * Description of UpdateService
 *
 * @author kaelinjacobson
 */
class UpdateService {
    protected $config;
    
    public function __construct($config) {
        $this->config = $config;
    }
    
    public function composerShouldUpdate() {
        return $this->config['composer']['shouldupdate'];
    }
    
    public function shouldSync() {
        return $this->config['sync'];
    }
    
    public function getGitBranch() {
        return $this->config['git']['branch'];
    }
    
    public function getGitRemote() {
        return $this->config['git']['remote'];
    }    
}