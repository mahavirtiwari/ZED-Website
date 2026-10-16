{{-- Special content of a page, chosen in CMS > Pages > Special content (page_widget). --}}
@php($embedUrl = (string) ($details->page_embed_url ?? ''))
@if (in_array($pageWidget, ['embed', 'embed_only'], true) && $embedUrl !== '')
    <iframe src="{{ $embedUrl }}" title="{{ $details->page_name }}" class="zed-embed-frame"></iframe>
@elseif ($pageWidget === 'parameters')
    @include('public.parameter-sector')
@elseif ($pageWidget === 'guidance')
    <div class="single-page-full-content">@include('public.guidance-document')</div>
@elseif ($pageWidget === 'maturity')
    @include('public.zed-maturity')
@elseif ($pageWidget === 'verify')

                    <!-- <form class="form-inline" style="margin-top:20px" action="/zee/verify-zed-certificate" method="post"> -->
                    <?php echo form_open('/'.$details->page_slug,array('class'=>'form-inline text-center','style'=>'margin-top:50px;margin-bottom:100px')); ?>

                      <div class="col-md-12" style="margin-bottom:20px">
                        <div class="form-group mb-5">
                          <label for="udyamNumber">Udyam Number: </label>
                          <input type="text" style="width:290px" class="form-control" id="udyamNumber" name="udyamNumber" value="<?php echo e($udyamNumber ?? ''); ?>" placeholder="Udyam Number">
                        </div>
                        <div class="form-group mx-sm-5 mb-5" style="margin-left: 15px;">
                          <label for="certificationNumber">Certification Number: </label>
                          <input type="text" style="width:290px" class="form-control" id="certificationNumber" name="certificationNumber" value="<?php echo e($certificationNumber ?? ''); ?>" placeholder="Certification Number">
                        </div>
                        <button type="submit" class="btn btn-primary mb-2">Submit</button>
                      </div>
                    <!-- </form> -->
                    <?php echo form_close();?>
                    <?php if(!empty($verifyCertification)){ ?>
                    <div class=" ">
                      <div class="col-md-12 jumbotron" >
                        <?php $ctf = $verifyCertification; 
                              //$ctf = json_decode($vctf["path"],true);
                              if(isset($ctf['StatusCode']) && $ctf['StatusCode'] == 200){
                        ?>
                        <h4><u>Plant Details</u></h4>
                        <p> Unit Name: 
                          <?php echo e($ctf["PlantDetails"]["UnitName"] ?? ''); ?>
                        </p>
                        <p> Address: 
                          <?php echo e($ctf["PlantDetails"]["PAddress"] ?? ''); ?>
                        </p>
                        <p> Pin: 
                          <?php echo e($ctf["PlantDetails"]["PPin"] ?? ''); ?>
                        </p>
                        <p> District Name: 
                          <?php echo e($ctf["PlantDetails"]["DISTRICT_NAME"] ?? ''); ?>
                        </p>
                        <p> State Name: 
                          <?php echo e($ctf["PlantDetails"]["state_name"] ?? ''); ?>
                        </p>
                        <br/>
                        <h4><u>Certification Details</u></h4>
                        <p> Certification Date: 
                          <?php echo e($ctf["CertificationDetails"]["CertificationDate"] ?? ''); ?>
                        </p>
                        <p> Date of Expiry: 
                          <?php echo e($ctf["CertificationDetails"]["ExpiryDate"] ?? ''); ?>
                        </p>
                        <p> Certification: 
                        <?php 
                          if(isset($ctf["CertificationDetails"]["BronzeCertified"]) && $ctf["CertificationDetails"]["BronzeCertified"] =="Yes"){
                              echo "Bronze";
                          } if(isset($ctf["CertificationDetails"]["SilverCertified"]) && $ctf["CertificationDetails"]["SilverCertified"] =="Yes"){
                              echo "Silver";
                          } if(isset($ctf["CertificationDetails"]["GoldCertified"]) && $ctf["CertificationDetails"]["GoldCertified"] =="Yes"){
                              echo "Gold";
                          }
                        ?>
                        </p>
                        <?php if(isset($ctf["CertificationDetails"]["Certificate"]) && !is_null($ctf["CertificationDetails"]["Certificate"]) ){?>
                        <p> Certificate: 
                          <?php echo e($ctf["CertificationDetails"]["Certificate"]); ?>
                        </p>
                        <?php } ?>
                        <?php }else{
                          echo "<h4 style='margin-top:10px;left:20%'><center>No Certificate found</center></h4>";
                        } ?>
                      </div>
                    </div>
                    <?php } ?>
                  
@endif
