import axios from "axios";

axios.interceptors.request.use(function(config) {
  
    config.headers.Authorization = sessionStorage.getItem('admin_token');
    return config;
    
},function(error) {
    console.log(error);
})

export default axios
