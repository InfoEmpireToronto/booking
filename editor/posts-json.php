<?php
class PostReader
{
	private $site;
	function __construct($site)
	{
		$this->site = $site;
	}
	function getPosts($type = 'news', $limit = null, $needle = null, $haystack = null)
	{
		$data = [
			'site_id' => $this->site,
			'type' => $type
		];

		if(is_array($limit))
		{
			$limit = implode(',', $limit);
		}
		if($limit)
		{
			$data['limit'] = $limit;
		}

		if($needle && $haystack)
		{
			if(is_array($haystack))
			{
				$haystack = implode(',', $haystack);
			}
			$data['needle'] = $needle;
			$data['haystack'] = $haystack;
		}

		$json = $this->curlPosts($data);
		$out = [];
		foreach($json->results as $item)
		{
			$out[] = new PostItem($item);
		}
		return $out;
	}
	function getPost($type, $id)
	{
		$data = [
			'site_id' => $this->site,
			'type' => $type,
			'id' => $id
		];
		$json = $this->curlPosts($data);
		$post = new PostItem($json->post);
		if($post->previous)
		{
			$post->previous = new PostItem($post->previous);
		}
		if($post->next)
		{
			$post->next = new PostItem($post->next);
		}
		return $post;
	}
	function getPostCount($type = 'news', $needle = null, $haystack = null)
	{
		$data = [
			'site_id' => $this->site,
			'type' => $type,
			'count' => 1
		];
		if($needle && $haystack)
		{
			if(is_array($haystack))
			{
				$haystack = implode(',', $haystack);
			}
			$data['needle'] = $needle;
			$data['haystack'] = $haystack;
		}
		$json = $this->curlPosts($data);
		return $json->count;
	}

	private function curlPosts($data)
	{
		/*
			Acceptable data keys:
			site_id	eg: 'site' => 10
			type		eg: 'type' => 'news'
			id			eg: 'id' => 52
			count		eg: 'count'
			limit		eg: 'limit' => '10,5'
			needle	eg: 'needle' => '+loyalty* +rewards*'
			haystack	eg: 'haystack' => 'title,content'
		*/
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://manager.infoempire.us/read/");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POSTREDIR, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		// 5 seconds before timing out
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec ($ch);
		
		curl_close($ch);
		$response = json_decode($response);
		return $response;
	}
}
class PostItem
{
	private $data;
	function __construct($item)
	{
		$this->data = $item;
		foreach($this->data as $prop => $val)
		{
			if(is_string($val))
			{
				$this->data->$prop = stripslashes($val);
			}
		}
	}
	function __get($property)
	{
		return $this->data->$property;
	}
	function __set($property, $value)
	{
		$this->data->$property = $value;
	}
	function getURL($prefix = 'blog-')
	{
		return $prefix . urlencode(trim($this->data->title)) . '-' . $this->data->id;
	}
	function getImage(&$start = null, &$length = null)
	{
		$text = $this->data->content;
		if(($imageStart = strpos($text, '<img')) !== false)
		{
			$start = $imageStart;
			$imageSourceStart = strpos($text, 'src=', $imageStart) + 5;
			$imageSourceLength = strcspn($text, "\"'", $imageSourceStart);
			$imageAltStart = strpos($text, 'alt=', $imageStart) + 5;
			if($imageAltStart)
			{
				$imageAltLength = strcspn($text, "\"'", $imageAltStart);
			}

			$imageEnd = strpos($text, '>', $imageStart) + 1;
			while(0 === strpos(substr($text, $imageEnd), '<br>'))
			{
				$imageEnd += 4;
			}

			$length = $imageEnd - $imageStart;
			return 
			[
				'src' => substr($text, $imageSourceStart, $imageSourceLength),
				'alt' => substr($text, $imageAltStart, $imageAltLength)
			];
		}
		else
		{
			return false;
		}
	}
	function extractImage()
	{
		$start = 0;
		$length = 0;
		$image = $this->getImage($start, $length);
		$this->data->content = substr_replace($this->data->content, '', $start, $length);
		return $image;
	}
	function getSummary($chars = 100)
	{
		$text = $this->data->content;
		// Change to the number of characters you want to display   
		$orig = strip_tags($text);  
		$text = $orig . " ";
		$text = substr($text, 0, $chars);
		$text = substr($text, 0, strrpos($text,' '));
	
		// Add ... if the text actually needs shortening
		if (strlen($orig) > $chars) {
				$text = $text."...";
		}
		return $text;
	}
}