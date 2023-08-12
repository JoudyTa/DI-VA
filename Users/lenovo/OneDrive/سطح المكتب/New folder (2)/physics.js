import { Vector3 } from "three";
export class RocketPhisycs{
    constructor( S , mf , mr , M ,  Ve , deltaM , Cb, tanb , w  ,windx , b , alfaaxis , waxis ,  lom ,g,
      lift_off = new Vector3(0,0,0) , alfa = new Vector3(0,0,0) 
    , Forceair = new Vector3(0,0,0) , Thrust = new Vector3(0,0,0) , 
    wight = new Vector3(0,0,0) ,position = new Vector3( 0 , -500 , 0 ) ,
     velocity = new Vector3(0,0,0) , acc = new Vector3(0,10,0)){

        this.position = position;
        this.velocity = velocity ;
        this.acc = acc ;
        this.Thrust = Thrust;
        this.wight = wight ;
        this.Forceair = Forceair;
        this.S = S;
        this.mf = mf ;
        this.M = M ;
        this.deltaM = deltaM ;
        this.mr = mr;
        this.Ve = Ve ;
        this.g = g ;
        this.tanb = tanb ;
        this.Cb = Cb ;
        this.alfa = alfa ;
        this.w = w ; 
        this.windx=windx;
        this.b=b;
        this.lift_off=lift_off;
        this.lom = lom ;
        this.alfaaxis = alfaaxis ; 
        this.waxis = waxis ;
    }

  setwindx(){
  let wind=0.1;//Km
   this.windx=wind*Math.cos(this.b);
    }
    getwindx(){
      return this.windx;
    }
    //around center of earth
    setangularV(){
      this.w = this.getalfaX / this.deltatime;

    }

    getangularV(){
      return this.w ;
    }
     
    setalfaY(){
      const re = 6378; 
      this.alfa.y = (this.getwightY() + this.getThrustY())/((re + this.position.y)(this.mf + this.mr));
    }

    getalfaY(){
      return this.alfa.y;
    }
   
    setangular(){
    const wind = 1.5 ;//km/h
    this.tanb = this.velocity.y / wind;
    this.b=Math.atan(this.tanb);
    this.Cb=90-this.b;
    }

    getangular(){
      return this.Cb;
    }

    //around its axis 

    setalfaaxis(){
      let r = Math.sqrt(this.S/3.14) ;
     this.alfaaxis = this.getliftY()*Math.sin(this.b)/((this.mr + this.mf)*r); // r of Rocket;
    }
    getalfaaxis(){
      return this.alfaaxis ;
    }

    setwaxis(){
      this.waxis = this.getalfaaxis()/this.deltatime ; 
    }
    //
     setliftX(){
      this.lift_off.x = this.getForceairX/this.tanb;
     }
     
     getliftX(){
      return this.lift_off.x;
     }
     
     setliftY(){
      this.lift_off.y = this.getForceairY/this.tanb;
     }

     getliftY(){
      return this.lift_off.y;
     }

     setg(){
      const g0 = 9.8;
      const R = 6378;
      this.g = g0 * (R/(R+(Math.abs(500+this.position.y ))))*(R/(R+(Math.abs(500+this.position.y ))));
     }

     getg(){
      return this.g;
     }

    setwightX(){
     this.M = this.mf + this.mr;
     this.wight.x = this.M*this.getg();
    }
    
    getwightX(){
        return this.wight.x;
    }

    setwightY(){
    
    this.M = this.mf + this.mr;
    this.wight.y = this.M*this.getg();
    }

    getwightY(){
        return this.wight.y;
    }

    setwightZ(){
      
        this.M = this.mf + this.mr;
        this.wight.x = this.M*this.getg();
    }

    getwightZ(){
        return this.wight.z;
    }

    setThrustX(){
        this.deltaM = this.mf - this.lom/60;
        this.Thrust.x = this.Ve*this.deltaM ;
        this.mf = this.deltaM ;
     }

    getThrustX(){
        return this.Thrust.x ;
    }

    setThrustY(){
        this.deltaM = this.mf - this.lom/60;
        this.mf = this.deltaM ;
        this.Thrust.y = this.Ve*this.deltaM ;
     }
 
     getThrustY(){
         return this.Thrust.y ;
     }

     setThrustZ(){
        this.deltaM = this.mf - this.lom/60;
        this.Thrust.z = this.Ve*this.deltaM ;
        this.mf = this.deltaM ;
     }
     getThrustZ(){
         return this.Thrust.z ;
     }
     setForceairX(){
       const K = 0.02;
       const Ro = 1.2 ;
       this.Forceair.x = 1/2*K*Ro*this.S*this.velocity.x*this.velocity.x;  
     }

     getForceairX(){
        return this.Forceair.x;
      }

     setForceairY(){
        const K = 0.02;
        const Ro = 1.2 ;
        this.Forceair.y = 1/2*K*Ro*this.S*this.velocity.y*this.velocity.y;  
      }

      getForceairY(){
        return this.Forceair.y;
      }

      setForceairZ(){
        const K = 0.02;
        const Ro = 1.2 ;
        this.Forceair.z = 1/2*K*Ro*this.S*this.velocity.z*this.velocity.z;  
      }

      
      getForceairZ(){
        return this.Forceair.z;
      }

      setaccX(){
        
        this.M = this.mf + this.mr;
        this.acc.x = (this.getThrustX() - this.getForceairX() - this.getwightX())/this.M;
     }

     getaccX(){
        return this.acc.x;
     }

      setaccY(){
        this.M = this.mf + this.mr;
        this.acc.y = (this.getThrustY() - this.getForceairY() - this.getwightY())/this.M;
     }

     getaccY(){
        return this.acc.y;
     }

     setaccZ(){
        this.M = this.mf + this.mr;
        this.acc.z = (this.getThrustZ() - this.getForceairZ() - this.getwightZ())/this.M;
     }

     getaccZ(){
        return this.acc.z;
     }
  
     update(deltatime){
        
         this.position.x += this.velocity.x * deltatime ;
         this.position.y += this.velocity.y * deltatime ;
         this.position.z += this.velocity.z * deltatime ;
   
        this.velocity.x += this.getaccX() * deltatime ;
        this.velocity.y += this.getaccY() * deltatime ;
        this.velocity.z += this.getaccZ() * deltatime ; 
     }
     
   }
   