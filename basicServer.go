package main

/*
  Created by: Fabián Doñaque
  Copyright by: Fabs Robotics SLU
  Created on: 2020-10-24
*/

/////////////////
//  Libraries  //
/////////////////

import (
  "log"
  "net/http"
)

////////////
//  Main  //
////////////

func main(){
  log.Println("Serving at port 13000")
  err := http.ListenAndServe(":13000",http.FileServer(http.Dir("./static/")))
  if err != nil { log.Panic(err) }
}
