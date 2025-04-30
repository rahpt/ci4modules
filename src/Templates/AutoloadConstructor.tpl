
    // --- modules-autoload (injcted by module:setup) ---
    public function __construct()
    {
        parent::__construct();

        foreach (glob(APPPATH . 'Modules/*', GLOB_ONLYDIR) as $dir) {
            $name = basename($dir);
            $this->psr4['__namespace__\\\\'.$name.'\\\\'] = $dir;
        }
    }
