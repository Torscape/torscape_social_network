<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<div class="footer" style="
margin: 10px 100px;
padding: 10px;
color: gray;
text-align: center;
border-top: 1px solid rgb(213, 213, 213);
">
<?php echo lang('site_title')?> &copy; <?php if(date("Y") == "2020"){echo '2020';}else{echo '2020-'.date("Y");} ?> All rights reserved.
</div>
