const token = localStorage.getItem('token');
if(token){
    window.location.href = "./views/home.php"
}

const form = document.getElementById('form');

form.addEventListener('submit', async(e)=>{
    e.preventDefault()

    const name = document.getElementById('name').value;
    const pass = document.getElementById('pass').value;

    try{

        const res = await axios.post("./controllers/auth.php", {
            username: name,
            password: pass
        }, {
            headers: {
                "Content-Type": "application/json"
            }
        })



        if (res.data.success) {
            localStorage.setItem("token", res.data.token);
            window.location.href = "./views/home.php";
            return;
        }

        alert(res.data.message || 'Login failed');


    }catch(err){
        console.error(err);
        const message = err.response?.data?.message || 'Login request failed';
        alert(message);
    }
})