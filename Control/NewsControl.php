<?php
	require_once '../Model/News.php';
	require_once '../DataBaseHandle/NewsServer.php';
	header("Content-Type: text/html;charset=utf-8");
	//session_start();
	Class NewsControl
	{
		public function JudgeOperate($operate)
		{
			switch($operate)
			{
				case "list":
                    NewsControl::GetAll();
					break;
				case "add":
                    NewsControl::AddNews();
					break;
				case "del":
                    NewsControl::DelNews();
					break;
				case "edit":
                    NewsControl::UpdateNews();
					break;
				case "query":
                    NewsControl::GetNews();
					break;
				case "gettype":
					NewsControl::GetType();
					break;
                case "top":
                    NewsControl::GoTop();
                    break;
                case "show":
                    NewsControl::ChangeShow();
                    break;
			}
		}

		public function GetAll()
		{
            $ns = new NewsServer();
            $result = $ns->GetAll();
            $re = array('state'=>'0','content'=>null);
            $jsonfile = fopen("../View/json/newsList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'title' => $n['title'], 'content' => $n['content'], 'type' => $n['type'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'operator'=>$n['operator'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'abstract'=>$n['abstract'],'keyword'=>$n['keyword'],'pic'=>$n['pic']);
                $re['content'] = $row;
                if (flock($jsonfile, LOCK_EX)) {//加写锁 
                    ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                    rewind($jsonfile); // 倒回文件指针的位置 
                    fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
                    flock($jsonfile, LOCK_UN); //解锁 
                }
            }
            fclose($jsonfile);
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

        public function GetType()
        {
            $ns = new NewsServer();
            $result = $ns->GetType();
            $re = array('state'=>'0','content'=>null);
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[]= array('id'=>$n['id'],'name'=>$n['name']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

        public function GetNews(){
		    $id = $_REQUEST['id'];
		    $ns = new NewsServer();
            $result = $ns->GetNewsById($id);
            $re = array('state'=>'0','content'=>null);
            while ($n = mysqli_fetch_array($result))
            {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'title' => $n['title'], 'content' => $n['content'], 'type' => $n['type'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'operator'=>$n['operator'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'abstract'=>$n['abstract'],'keyword'=>$n['keyword'],'pic'=>$n['pic']);
                $re['content'] = $row;
            }
            echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
        }

		public function UpdateNewsJson(){
            $ns = new NewsServer();
            $result = $ns->GetAll();
            $jsonfile = fopen("../View/json/newsList.json", "w") or die("Unable to open file!");
            while ($n = mysqli_fetch_array($result)) {
                $re['state'] = '1';
                $row[] = array('id' => $n['id'], 'title' => $n['title'], 'content' => $n['content'], 'type' => $n['type'], 'see' => $n['see'], 'top' => $n['top'],'show'=>$n['isshow'],'operator'=>$n['operator'],'created_at'=>$n['created_at'],'updated_at'=>$n['updated_at'],'abstract'=>$n['abstract'],'keyword'=>$n['keyword'],'pic'=>$n['pic']);
                $re['content'] = $row;
                if (flock($jsonfile, LOCK_EX)) {//加写锁 
                    ftruncate($jsonfile, 0); // 将文件截断到给定的长度 
                    rewind($jsonfile); // 倒回文件指针的位置 
                    fwrite($jsonfile, json_encode($row, JSON_UNESCAPED_UNICODE));
                    flock($jsonfile, LOCK_UN); //解锁 
                }
            }
            fclose($jsonfile);
		}

		public function AddNews()
		{
			$news = new News();
            $news->title = $_REQUEST['title'];
            $news->type = $_REQUEST['type'];
            $news->content = $str = str_replace('\'', '\"', $_REQUEST['content']);
            $news->show = $_REQUEST['show'];
            $news->top = $_REQUEST['top'];
            $news->created_at = $_REQUEST['created_at'];
            $news->updated_at = $_REQUEST['created_at'];
            $news->operator = $_REQUEST['operator'];
            $news->keyword = $_REQUEST['keyword'];
            $news->abstract = $_REQUEST['abstract'];
            $news->pic = $_REQUEST['pic'];
            $news->see = 0;
			$ns = new NewsServer();
            $re = array('state'=>'0','content'=>'添加失败');
            $result = $ns->InsertNews($news);
            if($result){
				$re['state'] = '1';
				$re['content'] = '添加成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
            return;
		}

		public function UpdateNews()
		{
			$news = new News();
            $news->id = $_REQUEST['id'];
            $news->title = $_REQUEST['title'];
            $news->operator = $_REQUEST['operator'];
            $news->created_at = $_REQUEST['created_at'];
            $news->type = $_REQUEST['type'];
            $news->keyword = $_REQUEST['keyword'];
            $news->abstract = $_REQUEST['abstract'];
            $news->pic = $_REQUEST['pic'];
            $news->content = $str = str_replace('\'', '\"', $_REQUEST['content']);
			$re = array('state'=>'0','content'=>'修改失败');
			$ns = new NewsServer();
			$result = $ns->UpdateNews($news,"all");
			if($result){
				$re['state'] = '1';
				$re['content'] = '修改成功';
			}
			echo json_encode($re,JSON_UNESCAPED_UNICODE);
			return;
		}

		public function DelNews(){
			$id = $_REQUEST['id'];
            $ns = new NewsServer();
            $result=$ns->DeleteNews($id);
            $re = array('state'=>'0','content'=>'删除失败');
            if($result) {
            	NewsControl::UpdateNewsJson();
                $re['state']='1';
                $re['content']='删除成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
		}

		public function GoTop(){
		    $id = $_REQUEST['id'];
            $top = $_REQUEST['top'];
            $ns = new NewsServer();
            $news = new News();
            $news->id = $id;
            $news->top = $top;
            $result = $ns->UpdateNews($news,"top");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                NewsControl::UpdateNewsJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }

        public function ChangeShow(){
            $id = $_REQUEST['id'];
            $show = $_REQUEST['show'];
            $ns = new NewsServer();
            $news = new News();
            $news->id = $id;
            $news->show = $show;
            $result = $ns->UpdateNews($news,"isshow");
            $re = array('state'=>'0','content'=>'修改失败');
            if($result) {
                NewsControl::UpdateNewsJson();
                $re['state']='1';
                $re['content']='修改成功';
            }
            echo  json_encode($re,JSON_UNESCAPED_UNICODE);
        }
	}
?>
