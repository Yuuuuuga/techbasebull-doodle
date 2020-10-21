<?php
// DB接続設定
$dsn = 'mysql:dbname=**********db;host=localhost';
$user = '************';
$password = 'ぱすわーど';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

/*テーブルを作る。*/
$sql = "CREATE TABLE IF NOT EXISTS tbbull"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	.");";
	$stmt = $pdo->query($sql);

/*以下は画面に表示されるような中身に関するところ。*/
/*編集中であるときのみ入力される何かを受け取った場合に入る。*/
if($_POST["hiddenediter"]!=null){
    $id = $_POST["hiddenediter"]; //変更する投稿番号
    $name = $_POST["name"];
    $comment = $_POST["comment"];//変更したい名前、変更したいコメントは自分で決めること
    $password = $_POST["password3"];
    $sql = 'UPDATE tbbull SET name=:name,comment=:comment,password=:password WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}
else{
    echo 1;
    /*submitボタンが押されたら入る。*/
    if(isset($_POST["submit"])){
        echo 2;
        /*nameとcommentとpasswordがnullでないなら入る。*/
        if($_POST["name"]!=null&&$_POST["name"]!=null){
            echo 3;
            /*テーブル内に文字を挿入する。*/
            $sql = $pdo -> prepare("INSERT INTO tbbull (name, comment) VALUES (:name, :comment)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);

            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $password = $_POST["password1"];
            $sql -> execute();
            echo 4;
        }
    }
    /*削除ボタンが押されたら入る。*/
    elseif(isset($_POST["delsub"])){
        /*名前わかんないやつ（テーブルの一個下の次元）の中からpasswordを抽出する。*/
        $id = $_POST["deletenum"]; //削除する投稿番号
        $sql = 'SELECT * FROM tbbull WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
    	foreach ($results as $row){
	    	//$rowの中にはテーブルのカラム名が入る
	    	$deletingnumpass = $row['password'];
	    }
	    /*抽出して、変数に代入したpasswordが受け取ったpassword2と合致するか確認。
	    合ってたら削除。*/
	    if($deletingnumpass==$_POST["password2"]){
            $id = $_POST["deletenumber"] ;
        	$sql = 'delete from tbbull where id=:id';
    	    $stmt = $pdo->prepare($sql);
    	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    	    $stmt->execute();
	    }else{
	        echo "passwordが間違っています。";
	    }
    }
    /*編集ボタンが押されたら入る。*/
    elseif(isset($_POST["editsub"])){
        /*名前わかんないやつ（テーブルの一個下の次元）の中からpasswordを抽出する。*/
        $id = $_POST["editnumber"]; //削除する投稿番号
        $sql = 'SELECT * FROM tbbull WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
    	foreach ($results as $row){
	    	//$rowの中にはテーブルのカラム名が入る
	    	$ingnumpass = $row['password'];
	    }
	    /*抽出して、変数に代入したpasswordが受け取ったpassword3と合致するか確認。
	    合ってたら削除。*/
	    if($ingnumpass==$_POST["password3"]){
            $id = $_POST["editnumber"]; //変更する投稿番号
            $sql = 'SELECT * FROM tbbull WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
    	    foreach ($results as $row){
	    	    //$rowの中にはテーブルのカラム名が入る
	        	$starteditingnum = $row['id'];
    		    $editname = $row['name'];
	        	$editcomment = $row['comment'];
	        }
	    }else{
	        echo "passwordが間違っています。";
	    }
    }
}
?>
<html lang=ja>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <form action="" method="POST">
        <input type="name" name="name" placeholder="ここに名前を入力。" value="<?php echo $editname ?>">
        <input type="text" name="comment" placeholder="ここにコメントを入力。" value="<?php echo $editcomment ?>">
        <input type="hidden" name="hiddenediter" value="<?php echo $starteditingnum ?>">
        
        <input type="submit" name="submit">
        
        <input type="number" name="deletenumber" placeholder="削除したい番号を入力。">
        <input type="password" name="password2">
        <input type="submit" name="delsub" value="削除">
        
        <input type="number" name="editnumber" placeholder="編集したい投稿番号を入力。">
        <input type="password" name="password3">
        <input type="submit" name="editsub" value="編集">
    </form>
</body>
</html>
<?php
/*テーブルの中身を表示する。*/
$sql = 'SELECT * FROM tbbull';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].',';
	echo $row['name'].',';
    echo $row['comment'].',';
    echo "<hr>";
}
?>