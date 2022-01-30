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
	"encoding/json"
	"io"
	"log"
	"net/http"
	"net/url"
	"os"
	"strings"
)

/////////////////
//  Constants  //
/////////////////

var ok int64 = 0
var serverError int64 = 1
var clientError int64 = 2
var unAuthorized int64 = 3

/////////////////
//  Variables  //
/////////////////

/////////////////////
//  Init function  //
/////////////////////


/////////////////
//  Functions  //
/////////////////

func response(w http.ResponseWriter, code int64, data interface{}){
	message := struct{
		Code int64 `json:"code"`
		Data interface{} `json:"data"`
	}{
		Code: code,
		Data: data,
	}
	msg, err := json.Marshal(message)
	if err != nil { w.Write([]byte("{'code':0,'data':'encoding error'}")); return }

	w.Write(msg)
}

func uploadFile(w http.ResponseWriter, r *http.Request){
	r.ParseMultipartForm(32 << 20)

	file, handler, err := r.FormFile("file")
	if err != nil { response(w,serverError,err.Error()); return }
	defer file.Close()

	f,err := os.OpenFile("downloads/"+handler.Filename,os.O_WRONLY|os.O_CREATE,0666)
	if err != nil { response(w,serverError,err.Error()); return }

	io.Copy(f,file)

	response(w,ok,"File uploaded")
}

func serveFile(w http.ResponseWriter, r *http.Request, path string){
	safePath,err := url.QueryUnescape(path)
	if err != nil { response(w,serverError, err.Error()); return }
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
	if strings.HasPrefix(path,"/api/uploadFile") {
		uploadFile(w,r)
	} else {
		response(w,clientError,"Access point does not exist.")
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
	port := "10000"
	log.Printf("Server start at port "+port)
	http.HandleFunc("/", mainRouter)
    if err := http.ListenAndServe(":"+port, nil); err != nil {
      log.Fatal(err)
    }
}

