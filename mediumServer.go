package main

/*
    Created by: Fabián Doñaque
    Copywrite: Fabs Robotics
    Created On: 2021-12-03
*/

///////////////
//  Imports  //
///////////////

import (
	"log"
	"net/http"
	"net/url"
	"strings"
)

/////////////////
//  Variables  //
/////////////////


/////////////////////
//  Init function  //
/////////////////////


/////////////////
//  Functions  //
/////////////////

func serveFile(w http.ResponseWriter, r *http.Request, path string){
	safePath,err := url.QueryUnescape(path)
	if err != nil { response.New(w,response.ServerError, err.Error()); return }
	http.ServeFile(w,r,safePath)
}

func getRouter(w http.ResponseWriter, r *http.Request){
	log.Printf("main - getRouter")
	
	path := r.URL.String()
	log.Printf("get: %v",path)
	
	serveFile(w,r,"static"+path)
}

func postRouter(w http.ResponseWriter, r *http.Request){
	log.Printf("main - postRouter")

	path := r.URL.String()
	log.Printf("post: %v",path)
	if strings.HasPrefix(path,"/api/function") { 
		function(w,r)
	} else {
		response.New(w,response.ClientError,"Access point does not exist.")
	}
}

func mainRouter(w http.ResponseWriter, r *http.Request) {
	log.Printf("main - mainRouter")
	if r.Method == http.MethodGet { 
		getRouter(w,r)
	} else if r.Method == http.MethodPost {
		postRouter(w,r)
	} else {
		w.Write([]byte("Method not allowed."))
	}
}

////////////
//  Main  //
////////////

func main(){
	port := "5000"
	log.Printf("Server start at port "+port)
	http.HandleFunc("/", mainRouter)
    if err := http.ListenAndServe(":"+port, nil); err != nil {
      log.Fatal(err)
    }
}
