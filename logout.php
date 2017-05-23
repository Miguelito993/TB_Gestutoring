<?php
    session_start();
    $prevID = $_SESSION['_id'];
    session_unset();
    session_destroy();
   
?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script>
        $.post('http://localhost:4242/changeStatus', {
            id: "<?php echo $prevID ; ?>",
            statusOnline: false
            },
            function(data){
                <?php unset($prevID); ?>
                window.location.replace("index.php");
            }
        );
</script>