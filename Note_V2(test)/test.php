<?php
session_start();
include 'db.php';
$name = $_SESSION['username'];
$id = $_SESSION['user_id'];



   // ------------------------------------------------------------------ Filter  ------------------------------------------------------------------

   $filter_name = isset($_POST['filter_name']) && $_POST['filter_name'] !== '' ? "AND task.title = '{$_POST['filter_name']}'" : '';
   $filter_category_id = isset($_POST['filter_category']) && $_POST['filter_category'] !== '' ? "AND categories.category_id = '{$_POST['filter_category']}'" : '';
   $filter_priority = isset($_POST['filter_priority']) && $_POST['filter_priority'] !== '' ? "AND priority.priority_id = '{$_POST['filter_priority']}'" : '';
   $date_start = isset($_POST['date_start']) && $_POST['date_start'] !== '' ? $_POST['date_start'] : '';
   $date_end = isset($_POST['date_end']) && $_POST['date_end'] !== '' ? $_POST['date_end'] : '';
   $filter_date = !empty($date_end) ? "AND DATE(transactions.date_time) BETWEEN '$date_start' AND '$date_end'" : '';




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ------------------------------------------------------------------ Insert Task ------------------------------------------------------------------ 
    if(isset($_POST['title'])){ 

        $title = $_POST['title'];
        $description = $_POST['description'];
        $due_date = $_POST['date'];
        $priority_id = $_POST['priority'];
        $status_id = 0;//$_POST['status'];
        $category_id = isset($_POST['category']) ? $_POST['category'] : 0;

        //-----------------------------------------INSERT---------------------------------------------------------------
        //ยัดข้อมูล
        $sql = "INSERT INTO task(user_id, title, description, due_date, priority_id, status_id, category_id) 
        VALUES('$id','$title', '$description', '$due_date', '$priority_id', '$status_id', '$category_id')";
        $result = mysqli_query($conn, $sql);
        //---------------------------------------------------------------------------------------------------------------
        
        header("location: test.php");
        //exit();  Important to stop further script execution after redirection
        
    }


    // ------------------------------------------------------------------  Insert Category ------------------------------------------------------------------ 
    if(isset($_POST['cate_name'])){

        $cate_name = $_POST['cate_name'];
        
        //-----------------------------------------INSERT---------------------------------------------------------------
        //ยัดข้อมูล
        $sql_cate = "INSERT INTO categories(user_id, name) 
        VALUES('$id','$cate_name')";
        $rs_cate = mysqli_query($conn, $sql_cate);
        //---------------------------------------------------------------------------------------------------------------
        header("location: test.php");
       
        
    }

    // ------------------------------------------------------------------  Delete Category ------------------------------------------------------------------ 
    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id']; // รับค่า category_id จากฟอร์ม

        //-----------------------------------------DELETE---------------------------------------------------------------
        $sql_d_cate = "DELETE FROM categories WHERE category_id='" . $category_id . "'";
        $rs_d_cate = mysqli_query($conn, $sql_d_cate);
        //---------------------------------------------------------------------------------------------------------------
        header("location: test.php");
    }



       

    // ------------------------------------------------------------------  Update Status ------------------------------------------------------------------ 

    if (isset($_POST['task_id'])) {
        $task_id = $_POST['task_id'];
        $status = isset($_POST['status']) ? 1 : 0; // ถ้า checkbox ถูกติ๊กจะให้ค่า status เป็น 1 ไม่เช่นนั้นจะเป็น 0

        //-----------------------------------------UPDATE---------------------------------------------------------------
        $update_status_sql = "UPDATE task SET status_id = '$status' WHERE task_id = '$task_id'";
        $result = mysqli_query($conn, $update_status_sql);
        //---------------------------------------------------------------------------------------------------------------
    }
    


    // ------------------------------------------------------------------  Show Task ------------------------------------------------------------------ 
    if (isset($_POST['task_up'])) {
        $_SESSION['task_up'] = $_POST['task_up'];
        $showModal = true; // ตั้งค่าเพื่อแสดงโมดอล
    } else {
        $showModal = false; // ถ้าไม่มีการตั้งค่า ไม่ต้องแสดงโมดอล
    }
    


     // ------------------------------------------------------------------  Update task ------------------------------------------------------------------ 

    if (isset($_POST['title_up'])) {
        $task_id = $_SESSION['task_up'];
        $title_up = $_POST['title_up'];
        $description_up = $_POST['description_up'];
        $due_date_up = $_POST['date_up'];
        $priority_up = isset($_POST['priority_up']) ? $_POST['priority_up'] : 0;
        $category_up = $_POST['category_up'] ;
    

        //-----------------------------------------UPDATE---------------------------------------------------------------
        $update_status_sql = "UPDATE task SET title = '$title_up', description = '$description_up', due_date = '$due_date_up'
        , priority_id = '$priority_up' , category_id = '$category_up'
        WHERE task_id = '$task_id'";
        $result = mysqli_query($conn, $update_status_sql);
        //---------------------------------------------------------------------------------------------------------------
    }
    
   
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    
    <title>ตัวติดตามรายรับ-รายจ่าย</title>
    <style>
        /*-------------- ส่วนตกเเต่ง sidebar-----------------------------*/
        .sidebar {
            position: fixed;
            width: 250px;
            background-color: #343a40;
            padding: 15px;
            height: 100vh;
        }
        .sidebar a, .sidebar button {
            color: #ffffff;
            padding: 10px;
            text-decoration: none;
            display: block;
            text-align: left;
        }
        .sidebar button {
            border: none;
            background: none;
        }
        .container {
            margin-left: auto; /* ดัน div ไปทางขวาสุด */
            margin-right: 50px; /* ปิดระยะห่างทางขวา */
        }

        /*-------------- ส่วนตกเเต่ง main-----------------------------*/
        body {
            display: flex;
        }
    </style>
</head>
<body>
    <!--------------------------------------------------------- Sidebar Bro----------------------------------------------------------->
    <div class="sidebar bg-dark p-3 text-light"><br><br>
        <h2 class="d-flex align-items-center">
            <img src="img/putna0003.png" alt="Profile Picture" class="rounded-circle" width="50" height="50">
            <span class="ms-3"><?php echo $_SESSION['username']; ?></span>
        </h2><br>

        <ul class="nav flex-column mt-4">
            <li class="nav-item mb-2">
                <a class="nav-link text-light" href="test.php"><i class="bi bi-bank"></i> งานทั้งหมด</a>
            </li>

            <li class="nav-item mb-2">
                <button class="nav-link text-light bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#taskModal">
                    <i class="bi bi-caret-down-square"></i> จัดการงาน
                </button>
            </li>

            <li class="nav-item mb-2">
                <button class="nav-link text-light bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="bi bi-clipboard-plus"></i> จัดการหมวดหมู่
                </button>
            </li>


            <li class="nav-item mb-2">
                <button class="nav-link text-light bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="bi bi-clipboard-plus"></i> ตัวกรอง
                </button> 
            </li><br><br><br><br><br><br><br><br><br><br><br><br><br>

            <li class="nav-item">
                <a class="nav-link text-light" href="login_web.php">ออกจากระบบ</a>
            </li>
            
        </ul>
    </div>
    

    <!--------------------------------------------------------- Main Bro----------------------------------------------------------->
    <div class="container mt-5 ">
        
        <div>

        <!---------------------------------------------- Task Management ------------------------------------------------------------------->
            <h1 class="text-center mb-4">Task Management</h1>
            <?php
            //-----------------------------------------SELECT---------------------------------------------------------------
            $sql_chk_task = "SELECT * FROM task WHERE user_id='" . $id . "'";
            $rs_chk_task = mysqli_query($conn, $sql_chk_task);
            //--------------------------------------------------------------------------------------------------------------



            //-----------------------------------------SELECT AND JOIN ----------------------------------------------------- 
            
            $get_name = "SELECT task.task_id, task.title, task.description, DATE(task.due_date) AS date, user.username AS user_name, 
                priority.priority_name, status.status_id, status.status_name, categories.name AS category_name
                FROM task
                JOIN user ON task.user_id = user.user_id
                JOIN priority ON task.priority_id = priority.priority_id
                JOIN status ON task.status_id = status.status_id
                JOIN categories ON task.category_id = categories.category_id 
                WHERE task.user_id='" . $id . "'
                $filter_category_id 
                $filter_priority
                $filter_name
                $filter_date
                ORDER BY task.due_date ASC
                ";
                 // ASC (จากเก่าไปใหม่) หรือ DESC (จากใหม่ไปเก่า)
                
            $rs_get = mysqli_query($conn, $get_name);
            //--------------------------------------------------------------------------------------------------------------

            if (mysqli_num_rows($rs_chk_task) == 0) {
                echo"<div class='text-center'>
                <h4>if you so handsome and smart plz click button Add Task</h4>
                        <h3>vvv</h3>
                </div><br>";
                echo"<div class='text-center'>
                
                        <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#taskModal'>
                            Add Task
                        </button>
                    </div>";
            } else {
                echo "<div class='table-responsive'>";
                echo "<table class='table  table-hover'>";
                echo "<thead class=''>
                        <tr>
                            <th> </th>
                            <th>Task Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                            <th> </th>
                            
                        </tr>
                    </thead>";
                echo "<tbody>";
                
                //ลออูปอ ไว้เเสดงค่า

                while ($row_task = mysqli_fetch_assoc($rs_get)) {
                    if($row_task['status_id'] == 0){
                        
                        echo "<tr>";

                        echo "<td>
                                <form action='' method='post'>
                                    <input type='hidden' name ='task_id' value='" . $row_task['task_id'] . "'>
                                    <input type='checkbox' name='status' value='1' onchange='this.form.submit()' " . ($row_task['status_id'] == 1 ? 'checked' : '') . ">
                                </form>
                            </td>";

                        echo "<td>" . $row_task['title'] . "</td>";
                        echo "<td >" . $row_task['category_name'] . "</td>";
                        echo "<td>" . substr($row_task['description'], 0, 10) . "</td>";
                        echo "<td>" . $row_task['date'] . "</td>";
                        echo "<td>" . $row_task['priority_name'] . "</td>";
                        echo "<td>
                                <form action='' method='POST'>
                                    <button type='submit' name='task_up' value='" . $row_task['task_id'] . "' >
                                        <i class='bi bi-list'></i>
                                    </button>
                                </form>
                            </td>";

                
                        echo "</tr>";
                    }
                
                }

                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                echo "<div class='text-center'>
                        <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#taskModal'>
                            Add Task
                        </button>
                    </div>";
            }
            ?>
        </div>

        <br><br><br><br><br><div class="container mt-5">
            <!---------------------------------------------- Clear Task Management ------------------------------------------------------------------->
            <h1 class="text-center mb-4">Clear Task Management</h1>

            <?php
            //-----------------------------------------SELECT---------------------------------------------------------------
            $sql_chk_task = "SELECT * FROM task WHERE user_id='" . $id . "'";
            $rs_chk_task = mysqli_query($conn, $sql_chk_task);
            //--------------------------------------------------------------------------------------------------------------
            
            //-----------------------------------------SELECT AND JOIN ----------------------------------------------------- 
            $get_name = "SELECT task.task_id, task.title, task.description,DATE(task.due_date) AS date, user.username AS user_name, 
                priority.priority_name, status.status_id, status.status_name, categories.name AS category_name
                FROM task
                JOIN user ON task.user_id = user.user_id
                JOIN priority ON task.priority_id = priority.priority_id
                JOIN status ON task.status_id = status.status_id
                JOIN categories ON task.category_id = categories.category_id 
                WHERE task.user_id='" . $id . "';";
            $rs_get = mysqli_query($conn, $get_name);
            //--------------------------------------------------------------------------------------------------------------

            if (mysqli_num_rows($rs_chk_task) == 0) {
                echo"<div class='text-center'>
                <h4>if you so handsome and smart plz click button Add Task</h4>
                        <h3>vvv</h3>
                </div><br>";
                echo"<div class='text-center'>
                
                        <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#taskModal'>
                            Add Task
                        </button>
                    </div>";
            } else {
                echo "<div class='table-responsive'>";
                echo "<table class='table  table-hover'>";
                echo "<thead class=''>
                        <tr>
                            <th> </th>
                            <th>Task Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                            
                        </tr>
                    </thead>";
                echo "<tbody>";
                
                //ลออูปอ ไว้เเสดงค่า
                while ($row_task = mysqli_fetch_assoc($rs_get)) {
                    if($row_task['status_id'] == 1){

                        echo "<tr>";

                        echo "<td>
                                <form action='' method='post'>
                                    <input type='hidden' name ='task_id' value='" . $row_task['task_id'] . "'>
                                    <input type='checkbox' name='status' value='1' onchange='this.form.submit()' " . ($row_task['status_id'] == 1 ? 'checked' : '') . ">
                                </form>
                            </td>";

                        echo "<td>" . $row_task['title'] . "</td>";
                        echo "<td>" . $row_task['category_name'] . "</td>";
                        echo "<td>" . substr($row_task['description'], 0, 10) . "</td>";
                        echo "<td>" . $row_task['date'] . "</td>";
                        echo "<td>" . $row_task['priority_name'] . "</td>";
                    
                        echo "</tr>";
                    }
                    
                }

                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                
            }
            ?>
        </div>
        <br><br><br><br><br>
    </div>

    
        
    

    <!------------------------------------------------- Modal add task -------------------------------------------------------------------------->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="taskName" class="form-label">Task Name</label>
                            <input type="text" class="form-control" id="taskName" name="title" placeholder="Enter task name" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="taskDescription" name="description" rows="3" placeholder="Enter description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="account">หมวดหมู่</label>
                            <select id="account" name="category" class="form-control">
                                <option value="">Null</option>
                                <?php

                                //-----------------------------------------SELECT---------------------------------------------------------------
                                // ดึงข้อมูล category มั้ง :)
                                $get_cate = "SELECT * FROM categories WHERE user_id = '" . $id . "'"; // จะเอาเเค่ category ของคนที่ login เท่านั้น
                                $rs_get_cate = mysqli_query($conn, $get_cate);
                                //--------------------------------------------------------------------------------------------------------------

                                while ($row_cate = mysqli_fetch_assoc($rs_get_cate)) {
                                    echo "<option value='{$row_cate['category_id']}'>{$row_cate['name']}</option>";
                                }
                                ?>
                            </select>
                            
                        </div>
                        <div class="mb-3">
                            <label for="dueDate" class="form-label">Due Date</label>
                            <input type="datetime-local" class="form-control" id="dueDate" name="date" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" value="1" id="priority" name="priority">
                            <label class="form-check-label" for="priority">Priority</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Task</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!------------------------------------------------- Modal add category ---------------------------------------------------------------------->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="categoryModalLabel">จัดการหมวดหมู่</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post">
                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="categoryName" name="cate_name" placeholder="กรอกชื่อหมวดหมู่" required>
                                    
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success btn-block" id="addCategoryBtn">
                                        <i class="fas fa-plus-circle"></i> เพิ่มหมวดหมู่
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <strong>รายการหมวดหมู่ปัจจุบัน</strong>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>หมวดหมู่</th>
                                        <th style="width: 150px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                               //-----------------------------------------SELECT---------------------------------------------------------------
                                // ดึงข้อมูล category มั้ง :)
                                $get_cate = "SELECT * FROM categories WHERE user_id = '" . $id . "'"; // จะเอาเเค่ category ของคนที่ login เท่านั้น
                                $rs_get_cate = mysqli_query($conn, $get_cate);
                                //--------------------------------------------------------------------------------------------------------------

                                        while ($row_cate = mysqli_fetch_assoc($rs_get_cate)) {
                                            echo "<tr>";
                                            echo "<th>" . $row_cate['name'] . "</th>";
                                            echo "
                                                    <th>
                                                        <form action='' method='post' onsubmit='return confirmDelete()'>
                                                            <input type='hidden' name='category_id' value='" . $row_cate['category_id'] . "'>
                                                            <button class='btn btn-danger btn-sm' type='submit' name='delete_category'>
                                                                <i class='fas fa-trash-alt'></i> ลบ
                                                            </button>
                                                        </form>
                                                    </th>";

                                            echo "</tr><br>";
                                            
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>



                
            </div>
        </div>
    </div>
<!------------------------------------------------- Modal filter -------------------------------------------------------------------------->



<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">filler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="taskName" class="form-label">Task Name</label>
                            <input type="text" class="form-control" id="taskName" name="filter_name" placeholder="Enter task name" >
                        </div>
                      
                        <div class="mb-3">
                            <label for="account">หมวดหมู่</label>
                            <select id="account" name="filter_category" class="form-control">
                            <option value="">ทั้งหมด</option>
                                <?php

                                //-----------------------------------------SELECT---------------------------------------------------------------
                                // ดึงข้อมูล category มั้ง :)
                                $get_cate = "SELECT * FROM categories WHERE user_id = '" . $id . "'"; // จะเอาเเค่ category ของคนที่ login เท่านั้น
                                $rs_get_cate = mysqli_query($conn, $get_cate);
                                //--------------------------------------------------------------------------------------------------------------

                                while ($row_cate = mysqli_fetch_assoc($rs_get_cate)) {
                                    echo "<option value='{$row_cate['category_id']}'>{$row_cate['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dueDate" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="dueDate" name="date_start">
                        </div>
                        <div class="mb-3">
                            <input type="date" class="form-control" id="dueDate" name="date_end" >
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" value="1" id="priority" name="filter_priority">
                            <label class="form-check-label" for="priority">Priority</label>
                        </div>
                        <a href="test.php">refilter</a>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Task</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


<!------------------------------------------------- Modal show & update -------------------------------------------------------------------------->
<!--<div class="modal fade <?php echo $showModal ? 'show' : ''; ?>" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="<?php echo $showModal ? 'false' : 'true'; ?>" style="<?php echo $showModal ? 'display: block;' : 'display: none;'; ?>"></div>-->


<div class="modal fade <?php echo $showModal ? 'show' : ''; ?>" id="showModal" tabindex="-1"   style="<?php echo $showModal ? 'display: block;' : 'display: none;'; ?>">
    
    <?php
        //-----------------------------------------SELECT---------------------------------------------------------------
        
        $task_up = $_SESSION['task_up'];
        $sql_up = "SELECT task.task_id, task.title, task.description, task.priority_id, task. due_date
        , task.category_id,categories.name AS category_name 
        FROM task 
        JOIN categories ON task.category_id = categories.category_id
        WHERE task_id = '" . $task_up . "'"; 
        $rs_shw_up = mysqli_query($conn, $sql_up);
        $up = mysqli_fetch_assoc($rs_shw_up);

        //--------------------------------------------------------------------------------------------------------------
    
    
    
    
    ?>
    <div class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Show Name</h5>
                    <form >
                        <button  class="btn-close" href="test.php" ></button>
                    </form>
                </div>

                
                <div class="modal-body">

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="taskName" class="form-label">Task Name</label>
                            <input type="text" class="form-control" id="taskName" name="title_up" value="<?php echo $up['title']; ?>" >
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="taskDescription" name="description_up" rows="3" ><?php echo $up['description']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="account">หมวดหมู่</label>
                            <select id="account" name="category_up" class="form-control">
                                <option value="<?php echo $up['category_id']; ?>"><?php echo $up['category_name']; ?></option>
                                <?php
                                //-----------------------------------------SELECT---------------------------------------------------------------
                                // ดึงข้อมูล category ของผู้ใช้ที่ login
                                $get_cate = "SELECT * FROM categories WHERE user_id = '" . $id . "'"; // จะเอาแค่ category ของคนที่ login เท่านั้น
                                $rs_get_cate = mysqli_query($conn, $get_cate);
                                //--------------------------------------------------------------------------------------------------------------

                                // วนลูปเพื่อสร้าง <option> สำหรับแต่ละ category
                                while ($row_cate = mysqli_fetch_assoc($rs_get_cate)) {
                                    // ข้ามตัวเลือกที่ตรงกับ category ที่เลือกไว้แล้ว
                                    if ($row_cate['category_id'] != $up['category_id']) {
                                        echo "<option value='{$row_cate['category_id']}'>{$row_cate['name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="dueDate" class="form-label">Due Date</label>
                            <input type="datetime-local" class="form-control" id="dueDate" name="date_up" value="<?php echo isset($up['due_date']) ? date('Y-m-d\TH:i', strtotime($up['due_date'])) : ''; ?>">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" value="1" id="priority" name="priority_up" <?php echo (isset($up['priority_id']) && $up['priority_id'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="priority">Priority</label>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>
    <style>
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.3); /* ปรับความโปร่งใส */
}
</style>




<!------------------------------------------------------------- script ---------------------------------------------------------------------------------------------------------------->
    <script>
function confirmDelete() {
    return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบหมวดหมู่นี้?');
}




</script>

</body>
</html>
