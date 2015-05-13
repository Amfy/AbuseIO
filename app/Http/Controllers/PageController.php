<?php namespace AbuseIO\Http\Controllers;

class PageController extends Controller {

    protected $viewdata = array(
        'nav_items' => array(
            'home'      => 'Home',
            'contacts'  => 'Contacts',
            'netblocks' => 'Netblocks',
            'domains'   => 'Domains',
            'reports'   => 'Reports',
            'search'    => 'Search',
            'analytics' => 'Analytics',
        ),
    );

	public function home()
	{
		return view('home', $this->viewdata);
	}

    public function contacts()
    {
        return view('contacts', $this->viewdata);
    }

    public function netblocks()
    {
        return view('netblocks', $this->viewdata);
    }

    public function domains()
    {
        return view('domains', $this->viewdata);
    }

    public function reports()
    {
        return view('reports', $this->viewdata);
    }

    public function search()
    {
        return view('search', $this->viewdata);
    }

    public function analytics()
    {
        return view('analytics', $this->viewdata);
    }

}
