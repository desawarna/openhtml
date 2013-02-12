<div id="control">
  <div class="control">
    <div class="buttons">
      <a id="account" class="tab button gap" href="<?php echo ROOT?>">New Page</a>

      <?php if ($ownership) :?>
         <?php if ($code_id) : ?>
            <a id="save" title="Save a new revision" class="button light save group left" href="<?php echo $code_id_path?>save">Save</a>
          <?php else : ?>
            <a id="save" class="tab button light save group left" href="<?php echo ROOT?>save">Save</a>
          <?php endif ?>
        <a id="view" target="<?php echo $code_id?>" class="tab button group light" href="http://<?php echo $_SERVER['HTTP_HOST'] . ROOT . $code_id?>">View</a>
      <?php else : ?>
      <a id="clone" title="Create a new copy" class="button clone group light left" href="<?php echo ROOT?>clone">Copy <?php echo $page_owner; ?>'s Page</a>
      <a id="view" target="<?php echo $code_id?>" class="tab button group light" href="http://<?php echo $_SERVER['HTTP_HOST'] . ROOT . $code_id?>">View</a>

      <?php endif ?>
       

        <?php if ($ownership) :?>
                <div class="button group gap right tall">
                <a id="options" class="title">Options</a>
                
                <a id="clone" title="Create a new copy" class="button clone group light" href="<?php echo ROOT?>clone">Copy</a>

            <?php else : ?>
                <div class="button group gap right tall">
                <a id="options" class="title">Options</a>
                
            <?php endif ?>
           <a id="download" title="Save to drive" class="button download group light" href="<?php echo ROOT?>download">Download</a>
           <a id="validatehtml" target="_blank" class="button group light" href="#">Validate HTML</a>
           <a id="validatecss" target="_blank" class="button group light" href="#">Validate CSS</a>

      </div> 
      
      
      <!--<a class="tab button source group left" accesskey="1" href="#source">Code</a>
      <a class="tab button preview group right gap" accesskey="2" href="#preview">Preview</a>-->
      <a title="Revert" class="button light group left" id="revert" href="#"><img class="enabled" src="<?php echo ROOT?>images/revert.png" /><img class="disabled" src="<?php echo ROOT?>images/revert-disabled.png" /></a>
    <!--<?php if ($code_id) : ?>
      <a id="jsbinurl" target="<?php echo $code_id?>" class="button group light left" href="http://<?php echo $_SERVER['HTTP_HOST'] . ROOT . $code_id?>"><?php echo $_SERVER['HTTP_HOST'] . ROOT . $code_id ?></a>


            <?php if ($ownership) :?>
             <div class="button group gap right tall">
                <a href="<?php echo ROOT?>save" class="save title">Save</a>
                <a id="save" title="Save a new revision" class="button light save group" href="<?php echo $code_id_path?>save">Save</a>
                <a id="clone" title="Create a new copy" class="button clone group light" href="<?php echo ROOT?>clone">Copy</a>

            <?php else : ?>

               <div class="button group gap right short">
                <a title="Create a new copy" class="clone title" href="<?php echo ROOT?>clone">Copy</a>
                <a id="clone" title="Create a new copy" class="button clone group light" href="<?php echo ROOT?>clone">Copy</a>

            <?php endif ?>
      <?php else : ?>
        <div class="button group gap left right">
          <a href="<?php echo ROOT?>save" class="save title">Save</a>
          <a id="save" title="Save new bin" class="button save group" href="<?php echo ROOT?>save">Save</a>
      <?php endif ?>
          <a id="download" title="Save to drive" class="button download group light" href="<?php echo ROOT?>download">Download</a>
          <!-- <a id="startingpoint" title="Set as starting code" class="button group" href="<?php echo ROOT?>save">As template</a> 
      </div>  -->

      <span id="panelsvisible" class="gap">Show:
        <input type="checkbox" data-panel="javascript" data-uri="css" id="showjavascript"><label for="showjavascript">CSS</label>
        <input type="checkbox" data-panel="html" data-uri="html" id="showhtml"><label for="showhtml">HTML</label>
        <input type="checkbox" data-panel="live" data-uri="live" id="showlive"><label for="showlive">Preview</label>
      </span>

      <span id="zoomout" class="button light group left sizer" data-delta="-3">
        -
      </span>
      <span id="reset" class="button light group sizer" data-delta="0">
        =
      </span>
      <span id="zoomin" class="button light group right sizer" data-delta="3">
        +
      </span>

      <div id="userinfo">
        <a id="account" class="button group light left" href="<?php echo ROOT?>list">Page List<?php //echo $is_owner?></a> 
        <!--<a id="account" class="button group light" href="<?php echo ROOT?>list"><?php echo $_SESSION['name']; ?></a> -->
        <div class="button group gap right tall">
          <a id="admin" class="title" href="#"><?php echo $_SESSION['name']; ?></a>
          <?php if($dash) : ?><a id="dashboard" title="Dashboard" class="button light group" href="<?php echo ROOT?>dashboard">Dashboard<?php endif ?>
          <a id="change" title="Change Password" class="button light group" href="<?php echo ROOT?>changepassword">Password</a>
          <a id="logout" title="Logout" class="button group light" href="<?php echo ROOT?>logout">Logout</a>
        </div>

      <span id="logo">openHTML</span>
    </div>
    </div>
  </div>
</div>







