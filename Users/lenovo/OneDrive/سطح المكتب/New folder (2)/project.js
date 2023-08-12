//import * as THREE from "three";
let scene , camera , renderer , skyboxGeo , skybox  , delta=0 , clock = new THREE.Clock() ;
let avtar;
let Rocket;
function init(){
  scene = new THREE.Scene();
  camera = new THREE.PerspectiveCamera(55 , window.innerWidth/window.innerHeight , 0.1 , 300);
 // camera.position.set(-90 , -20 , -90);

  renderer = new THREE.WebGLRenderer({antialias:true});
  renderer.setSize(window.innerWidth , window.innerHeight);
  document.body.appendChild(renderer.domElement);

 // let controls = THREE.OrbitControls(camera);
  //controls.addEventListener('change' , renderer);
  //controls.minDistance(500); 
  //controls.maxDistance(1500);


   let materialArray=[];

   let texture_ft = new THREE.TextureLoader().load('texture/arid_ft.jpg');
   let texture_bk = new THREE.TextureLoader().load('texture/arid_bk.jpg');
   let texture_up = new THREE.TextureLoader().load('texture/arid_up.jpg');
   let texture_dn = new THREE.TextureLoader().load('texture/arid_dn.jpg');
   let texture_rt = new THREE.TextureLoader().load('texture/arid_rt.jpg');
   let texture_lt = new THREE.TextureLoader().load('texture/arid_lf.jpg');

   materialArray.push(new THREE.MeshBasicMaterial({map:texture_ft}));
   materialArray.push(new THREE.MeshBasicMaterial({map:texture_bk}));
   materialArray.push(new THREE.MeshBasicMaterial({map:texture_up}));
   materialArray.push(new THREE.MeshBasicMaterial({map:texture_dn}));
   materialArray.push(new THREE.MeshBasicMaterial({map:texture_rt}));
   materialArray.push(new THREE.MeshBasicMaterial({map:texture_lt}));
   
   for(let i=0 ; i<6 ; i++){
   materialArray[i].side = THREE.BackSide;
   }

  skyboxGeo = new THREE.BoxGeometry(1000 , 1000, 1000);
// material = new THREE.MeshBasicMaterial({color:0x0000ff});
  skybox = new THREE.Mesh(skyboxGeo , materialArray);
  scene.add(skybox); 
 
 //inputs





  document.body.addEventListener('keydown', keyPressed);
  drawRokcet();
  done();
 
}
function keyPressed(e){
  switch(e.key){
    case 'ArrowUp':
      camera.rotation.x+=0.01;
      break;
    case 'ArrowDown':
      camera.rotation.x-=0.01;
      break;
    case 'ArrowLeft':
      camera.rotation.y+=0.01;
      break;
    case 'ArrowRight':
      camera.rotation.y-=0.01; 
      break;
    case 'w':
      camera.translateZ(-2);
      break;
    case 's':
      camera.translateZ(+2);
      break;
    case 'd':
      camera.translateX(+2);
      break;
    case 'a':
      camera.translateX(-2 );             
  }
} 

function drawRokcet(){
  var loader = new THREE.GLTFLoader();
  loader.load(
    "models/scene.gltf",
    function (gltf) {
      avtar = gltf.scene; 
      avtar.position.z = +400;
      avtar.position.y = -500;
      scene.add(avtar);
    }
  );
  // for light
      var hemiLight = new THREE.HemisphereLight( 0xffffff, 0xffffff, 0.61 );
      hemiLight.position.set( 0, 50, 0 );
// Add hemisphere light to scene   
      scene.add( hemiLight );

      var dirLight = new THREE.DirectionalLight( 0xffffff, 0.54 );
       dirLight.position.set( -8, 12, 8 );
       dirLight.castShadow = true;
       dirLight.shadow.mapSize = new THREE.Vector2(1024, 1024);
// Add directional Light to scene    
        scene.add( dirLight );

  camera.rotation.y = 380;
  camera.position.y = -450;
  camera.position.z = +270;
  
}

//import {positionC, positionH , update} from './physics.js'
import {RocketPhisycs} from "./physics.js";
function done(){
let  S = document.getElementById('S').value;
let mf = document.getElementById('mf').value;
let mr = document.getElementById('mr').value;
let ve = document.getElementById('ve').value;
let lom = document.getElementById('lom').value;
Rocket = new RocketPhisycs(S , mf , mr , mf + mr , ve , mf-lom/60 , 1 ,  1 , 1 , 1 , 1 , 1 , 1 , lom , 9.8);
function animate(){ 

  if(avtar){
    if(Rocket.getThrustY()<Rocket.getForceairY() + Rocket.getwightY()){
      requestAnimationFrame(null);
    }
    avtar.position.y = Rocket.position.y;
    camera.position.y = Rocket.position.y;
    if(Rocket.mf <= 0){
    requestAnimationFrame(null);
   }
   } 
  Rocket.setg();
  Rocket.setForceairY();
  Rocket.setThrustY();
  Rocket.setwightY();
  Rocket.setaccY(); 
  document.getElementById('h3').innerHTML = Rocket.velocity.y ; 
 // console.log(Rocket.velocity.y);
 // console.log(Space);
 delta = clock.getDelta();
  Rocket.update(delta);



  renderer.render(scene , camera); 
  requestAnimationFrame(animate);

}
animate();
}
//init();

document.querySelector('button').addEventListener('click' , function(){
  setTimeout(function(){
    init();
  } , 1000);
})


