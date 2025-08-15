<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>EXAMS SPACE</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
        }

        main.main-content {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 280px;
            max-width: 90vw;
            width: 320px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        a {
            display: inline-block;
            margin: 10px 15px;
            padding: 10px 25px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #0056b3;
        }

        @media (max-width: 400px) {
            main.main-content {
                width: 90vw;
                padding: 15px 10px;
            }

            a {
                display: block;
                margin: 10px auto;
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <main class="main-content" id="mainContent">
        <h2>Exam Menu</h2>
        <a href="Add.php">إدخال العلامات</a>
        <a href="edit_marks.php">تعديل العلامات</a>
        <a href="view_marks.php">عرض العلامات</a>
    </main>
</body>
</html>