<?php

$last = null;
$board = [
    [3,3,3],
    [3,3,3],
    [3,3,3],
];

$win_conditions = [
    [[0,0],[0,1],[0,2]],
    [[1,0],[1,1],[1,2]],
    [[2,0],[2,1],[2,2]],
    [[0,0],[1,0],[2,0]],
    [[0,1],[1,1],[2,1]],
    [[0,2],[1,2],[2,2]],
    [[0,0],[1,1],[2,2]],
    [[0,2],[1,1],[2,0]],
];

function create_tile($value, $id){
    $select = "";
    $name = "row_". $id;
    $realValue = null;
    if($value == 0){
        $realValue = "O";
    }else if($value == 1){
        $realValue = "X";
    }else{
        $realValue = "select";
    }
    if($value == 0 || $value == 1){
        $select .= "<input type='hidden' name='".$name."' value='".$realValue."'/>";
        $select .= "<select disabled='disabled'>";
    }else{
        $select .= "<select name='".$name."'>";
    }
    $select .= "<option>Choisir</option>";
    if($value == 0){
        $select .= "<option selected='selected'>O</option>";
    }else{
        $select .= "<option>O</option>";
    }
    if($value == 1){
        $select .= "<option selected='selected'>X</option>";
    }else{
        $select .= "<option>X</option>";
    }
        $select .= "</select>";
            return $select;
}

function check_winner($conditions, $response){
    $lr = null;
    $matches = [];
    for($i=0; $i<=count($conditions) - 1; $i++){
        foreach($conditions[$i] as $rows){
            $x = $rows[0];
            $y = $rows[1];
            if($response[$x][$y] != 3){
                if($lr == $response[$x][$y] || $lr == null){
                    $matches[] = $response[$x][$y];
                    $lr = $response[$x][$y];
                }else{
                    $lr = null;
                    $matches = [];
                    continue;
                }
            }
        }
        if(count($matches) == 3){
            if($matches[0] == $matches[1] && $matches[1] == $matches[2]){
                return true;
            }
        }else{
            $matches = [];
            $lr = null;
        }
    }
    return false;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['play'])){
    $board = isset($_POST['board']) ? json_decode($_POST['board']) : [];
    $last = isset($_POST['last']) ? $_POST['last'] : null;
    $responses = [];
    $rowarray = [];
    $counter = 0;
    /*echo "<pre>";
    print_r($_POST);
    echo "</pre>"; exit;*/
    foreach($_POST as $key=>$value){
        if(!in_array($key, ["board", "play", "last"])){
            if($value == 'O'){
                $rowarray[] = 0;
            }else if($value == 'X'){
                $rowarray[] = 1;
            }else{
                $rowarray[] = 3;
            }
            $counter++;
            if($counter % 3 == 0){
                $responses[] = $rowarray;
                $rowarray = [];
            }
        }
    }

    $changes = [];
    for($i=0;$i<=count($board)-1; $i++){
        foreach($board[$i] as $key=>$value){
            if($value != $responses[$i][$key]){
                $changes[] = $responses[$i][$key];
            }
        }
    }

    if(count($changes) == 0){
        echo "Veuillez choisir une case.";
    }
    if(count($changes) > 1){
        echo "Vous ne pouvez jouer qu'une case par tour !";
    }else if($last != null && $last == $changes[0]){
        echo "Vous ne pouvez jouer qu'une fois par tour !";
    }else if(check_winner($win_conditions, $responses)){
        $last = $changes[0];
        $board = $responses;
        if($last == 0)
        echo "O a gagné !";
        if($last == 1)
        echo "X a gagné !";
        if($last == 0 || $last == 1)
        echo "<a href='game.php'>Nouvelle partie</a>";
        return;
        //var_dump($last);
        /*echo "<pre>";
        print_r($win_conditions);
        echo "</pre>";*/
    }else{
        $last = $changes[0];
        $board = $responses;
    }
    /*echo"<pre>";
    print_r($responses);
    echo"</pre>";*/
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        <title>Morpion</title>
    </head>
<body>
<h1>Morpion</h1>

<form method="post">
    <input name="board" type="hidden" value="<?php echo json_encode($board);?>" />
    <input name="last" type="hidden" value="<?php echo $last;?>"/>
    <table class="table">
        <?php $count=1; foreach($board as $row):?>
        <tr>
            <?php foreach($row as $tile):?>
            <td>
                <?php echo create_tile($tile, $count);?>
            </td>
            <?php $count++; endforeach;?>
        </tr>
        <?php endforeach;?>
    </table>
        <button name="play">Jouer</button>
</form>

<div class="repo">
    <ul><li><a href="https://github.com/maxime-hadj/tictactoe">Repo Github</a></li></ul>
</div>

</body>
</html>