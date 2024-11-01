<?php

class YKS_Search{

	const DEBUG_MODE = 0;

	public $query = NULL;
	public $settings = array();
	public $mods = array();
	public $mod = array();
	public $active = NULL;
	public $title = array();
	public $title_sep;

	function __construct(){

		$this->title_sep = __('&amp;');

		$mods = get_option('yks-form-settings');

		$this->mods = $mods;

		$this->active = (isset($_GET['yks'])&&$_GET['yks']==1)?TRUE:FALSE;

		add_filter( 'get_search_query',array($this,'search_title'), 1000, 2 );
	}

	public function search_any_field($where){
		global $wpdb;
		$pat = "/\)\)\)\s*AND\s*\(\s*".$wpdb->postmeta."\s*=\s*'(.*?)'/";
		$rep = "OR(".$wpdb->postmeta."='$1')))";
		//$rep = "OR (".$wpdb->postmeta."\s*'$1'\)\)\)";

		$where = preg_replace($pat, $rep , $where);
		
		return $where;
	}

	public function set_query( $query ) {
		global $wp_post_types;

    	if ( is_search() && $query->is_main_query()){


	    		$s = (isset($_GET['s']))?sanitize_text_field($_GET['s']):'';//他の検索フォームが使われた場合の処理
	    		$query->set('s' , $s);

	    		//default args
	    		$meta_query = array('relation' => 'AND');
	    		$tax_query = array('relation' => 'AND');
	    		$post_type = 'any';
	    		$s = '';

	    		foreach($this->mods as $mod){
		    		if($mod['type'] == 'keyword'){
		    			$s = (!empty($_GET['mod_s-'.$mod['mod_id']]))?sanitize_text_field($_GET['mod_s-'.$mod['mod_id']]):'';
		    			if($s)$this->title[] = $s; 
		    			//add_filter( 'posts_where', array($this , 'search_any_field' ));//AND -> OR
		    		}


		    		//add post type
		    		$pt = (!empty($_GET['mod_pt-'.$mod['mod_id']]))?sanitize_text_field($_GET['mod_pt-'.$mod['mod_id']]):'';   
		    			if($mod['type'] == 'post_type'  && !empty($pt)){
		    				$post_type = $pt;
		    				$pto = $wp_post_types[$pt];
		    				$this->title[] = $pto->label;
		    			}


		    		//add meta query
		    		$mv = (!empty($_GET['mod_mv-'.$mod['mod_id']]))?(array)$_GET['mod_mv-'.$mod['mod_id']]:array();    		

			    			if($mod['type'] == 'meta_query'){
					    		//meta query (equal)
					    		$meta_query[$mod['meta_key'].'-cls'] = array(//sortのために必要
						    				'key' => $mod['meta_key'],
						    				);
					    		if(count($mv)==1){//単一値の場合
						    			if($mv[0] != 'all')$meta_query[$mod['meta_key'].'-cls']['value'] = sanitize_text_field($mv[0]);
						    			$this->title[]=$mv[0];
					    		}else if(count($mv)>=2){//chkboxの場合
					    				foreach ($mv as $v) {
					    					$meta_query[] = array(
						    				'key' => $mod['meta_key'],
						    				'value' => sanitize_text_field($v)
						    				);
						    				$this->title[]=$v;
					    				}
					    			}
					    		}
				    		
					    	//range
					    	if($mod['type'] == 'range'){
					    				
					    				$meta_query[$mod['meta_key'].'-cls'] = array(
									    				'key' => $mod['meta_key'],
									    				'type'=> 'NUMERIC'
									    				);

					    				if(count($mv)==1){
					    					$mve = explode('-', $mv[0]);

					    						$min = intval($mve[0]);
					    						$max = intval($mve[1]);

					    						if($mv[0] != 'all'){
									    			if(!empty($min)){
										    			$meta_query[$mod['meta_key']][] = array(
										    				'key' => $mod['meta_key'],
										    				'type' => 'NUMERIC',
										    				'value' => sanitize_text_field($min),
										    				'compare' => '>'
										    				);
									    			}
									    			if(!empty($max)){
											    			$meta_query[$mod['meta_key']][] = array(
											    				'key' => $mod['meta_key'],
											    				'value' => sanitize_text_field($max),
											    				'type' => 'NUMERIC',
											    				'compare' => '<'
									    					);
								    					}
								    			}else{
									    			
						    					}


						    				
						    			$this->title[]=$mv[0];
					    			}else if(count($mv)>=2){ //想定しない
					    				// foreach ($mv as $v) {
					    				// 	$meta_query[] = array(
						    			// 	'key' => $mod['meta_key'],
						    			// 	'value' => sanitize_text_field($v)
						    			// 	);
						    			// 	$this->title[]=$v;
					    				// }
					    			}
					    		
					    		
				    		}

				    	

		    		//add tax query
		    		$terms = (!empty($_GET['mod_term-'.$mod['mod_id']]))?sanitize_text_field($_GET['mod_term-'.$mod['mod_id']]):'';

		    		if((($mod['type'] == 'tax_query')||($mod['type'] == 'category')||($mod['type'] == 'post_tag')) && !empty($_GET['mod_term-'.$mod['mod_id']])){  			
		    			$tax_query[$mod['taxonomy'].'-cls'] = array(
		    				'taxonomy' => $mod['taxonomy'],
		    				'field' => 'id',
		    				'terms' => $terms,
		    				'operator' => 'IN',
		    				'include_children' => true
		    				);

		    			foreach((array)$terms as $term){
		    				$to = get_term( $term, $mod['taxonomy']);
		    				$this->title[] = $to->name;
		    			}
		    		}

		    		//add orderby for winsb

		    		// $meta_query['rating-cls'] = array(//sortのために必要
						  //   				'key' => 'rating',
						  //   				);
		    		$orderby_v = (!empty($_GET['mod_orderby']))?sanitize_text_field($_GET['mod_orderby']):'';

		    		$orderby = array();

		    		if($orderby_v){
		    			$e = explode('+', $orderby_v);
		    			$orderby[$e[0]] = $e[1]; 
		    		}
		    		//

	    		}//end mods loop
	    		$query->set('s' , $s);
	    		$query->set('post_type' , $post_type);
	    		$query->set('meta_query' , $meta_query);
	    		$query->set('tax_query' , $tax_query);
//var_dump($orderby);
	    		$query->set('orderby' , $orderby);

			}
			$this->query = $query;

		// if(self::DEBUG_MODE){
		// 	add_action( 'wp_footer', array($this , 'debug'), 100 , 1 );
		// }
	}


	public function alter_query(){
		if($this->active){
			add_action( 'pre_get_posts', array($this , 'set_query'),100);
		}
	}


	public function search_title($s) {
    	if(is_search() && is_main_query() && $this->title){
    		$s = $this->generate_search_title($s);
    	}
    	return $s;
	}


	public function generate_search_title($s){
			$t = implode($this->title_sep , $this->title);
		return $t;
	}

}

