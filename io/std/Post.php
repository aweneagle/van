<?
namespace std;

class Post extends PregFilter {
    public function __construct(){
        $this->set_input($_POST);
    }
}
