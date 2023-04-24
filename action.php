<?php
	header("Content-type: application/json");
    if (isset($_GET['action'])) {

        $conn = new mysqli("localhost","root","","sim_election");
        if ($conn->connect_error) {
            die('Database connect failed....');
        }

        $action = $_GET['action'];
		$res = [];
        $cres = [];
		$id = isset($_GET['id']) ? $_GET['id']:'';
		$search = isset($_GET['search']) ? $_GET['search']:'';
		$sort = isset($_GET['sort']) ? $_GET['sort']:'';

        if ($action=='read') {
			if (empty($id)) {

				$stmt = "SELECT * FROM party_list WHERE deleted_at is null";
                $cdata = "SELECT pname, scores FROM party_list WHERE deleted_at is null";

				if (!empty($search)) {
					$stmt.=" 
						AND CONCAT(pname, candidate) LIKE '%${search}%' 
						OR candidate LIKE '%${search}%'
					";
				}
				if (!empty($sort)) {
					$stmt.=" ORDER BY ${sort}";
				}

				$results = $conn->query($stmt);
                $cresults = $conn->query($cdata);

                if ($cdata) {
                    $chart = [];
					while ($roww = $cresults->fetch_assoc()) {
						array_push($chart, $roww);
					}
                    $cres['ccode'] = 200;
                    $cres['cmessage'] = 'read chart data success';
                    $cres['cdata'] = $chart;
                }else{
					$res['ccode'] = 500;
					$res['cmessage'] = 'read chart data failed';
				}

				if ($results) {
					$party = [];
					while ($row = $results->fetch_assoc()) {
						array_push($party, $row);
					}
					$res['code'] = 200;
					$res['message'] = 'read success';
					$res['data'] = $party;
				}else{
					$res['code'] = 500;
					$res['message'] = 'read failed';
				}
			} else {
				$results = $conn->query(
					"SELECT * FROM party_list WHERE deleted_at is null AND id=".$id
				);
				if ($results) {
					$party = '';
					while ($row = $results->fetch_assoc()) {
						$party = $row;
					}
					$res['code'] = 200;
					$res['message'] = 'read success';
					$res['data'] = $party;
				}else{
					$res['code'] = 500;
					$res['message'] = 'read failed';
				}
			}
			

			echo json_encode($res);
			die();
		}

        if ($action=='create') {
			if 
			(
				!isset($_POST['pname']) || empty($_POST['pname']) ||
				!isset($_POST['candidate'])|| empty($_POST['candidate']) ||
				!isset($_POST['policy']) || empty($_POST['policy']) || empty($_FILES)
			) 
			{
				$res['code'] = 422;
				$res['massage'] = 'some infomation is empty';
				echo json_encode($res);
				die();
			}
			$pname = $_POST['pname'];
			$candidate = $_POST['candidate'];
			$policy = $_POST['policy'];
            $scores = $_POST['scores'];
			$avatar = $_FILES['avatar']['name'];
			$exp_avatar = explode('.',$avatar);
			$date = Date('Y-m-d H:I:s',time());
			$rand = rand(10000,99999);
			$name_avatar = md5($date.$rand).'.'.$exp_avatar[1];
			$path_upload = 'uploads/'.$name_avatar;

			move_uploaded_file($_FILES['avatar']['tmp_name'], $path_upload);

			$result = $conn->query(
				"INSERT INTO party_list 
					(
						pname,
						candidate,
						policy,
						avatar,
                        scores
					)
					VALUES 
					(
						'$pname',
						'$candidate',
						'$policy',
						'$name_avatar',
                        '$scores'
					)
				");
			if ($result) {
				$res['code'] = 200;
				$res['message'] = 'party added success';
			}else{
				$res['code'] = 500;
				$res['message'] = 'party add failed';
			}
			echo json_encode($res);
			die();
		}

        if ($action=='delete') {
			if (isset($_POST['id']) && !empty($_POST['id'])) {
				$id = $_POST['id'];
				$date = Date('Y-m-d');
				$stmt ="UPDATE party_list set deleted_at='$date' WHERE id='$id'";
				$result = $conn->query($stmt);
				if ($result) {
					$res['code'] = 200;
					$res['message'] = 'student Deleted success';
				}else{
					$res['code'] = 500;
					$res['message'] = 'student delete failed';
				}
				echo json_encode($res);
				die();
			}
		}

        if ($action=='update') {
			if 
			(
				!isset($_POST['pname']) ||
				empty($_POST['pname']) ||
				!isset($_POST['candidate'])||
				empty($_POST['candidate']) ||
				!isset($_POST['policy']) ||
				empty($_POST['policy']) ||
				!isset($_POST['id']) ||
				empty($_POST['id'])
			) 
			{
				$res['code'] = 422;
				$res['massage'] = 'some infomation is empty';
				echo json_encode($res);
				die();
			}
			$pname = $_POST['pname'];
			$candidate = $_POST['candidate'];
			$policy = $_POST['policy'];
			$id = $_POST['id'];

			$stmt = "
					UPDATE party_list SET 
					pname='$pname',
					candidate='$candidate',
					policy='$policy'
					WHERE id = '$id'

				";

			if (!empty($_FILES)) {
				$avatar = $_FILES['avatar']['name'];
				$exp_avatar = explode('.',$avatar);
				$date = Date('Y-m-d H:I:s',time());
				$rand = rand(10000,99999);
				$name_avatar = md5($date.$rand).'.'.$exp_avatar[1];
				$path_upload = 'uploads/'.$name_avatar;
				move_uploaded_file($_FILES['avatar']['tmp_name'], $path_upload);

				$stmt = "
					UPDATE party_list SET 
					pname='$pname',
					candidate='$candidate',
					policy='$policy',
					avatar='$name_avatar'
					WHERE id = '$id'
				";

			}

			$result = $conn->query($stmt);
			if ($result) {
				$res['code'] = 200;
				$res['message'] = 'student updated success';
			}else{
				$res['code'] = 500;
				$res['message'] = 'student update failed';
			}
			echo json_encode($res);
			die();


		}

    }


?>