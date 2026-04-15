const token = localStorage.getItem('token');
if(!token){
    window.location.href = "../index.php"
}

const logout = document.getElementById('logout');
logout.addEventListener('click', ()=>{
    localStorage.removeItem('token');
    window.location.href = "../index.php"
})


const formBooking = document.getElementById('formBooking');
const bookingSubmit = document.getElementById('bookingSubmit');
const displayData = document.getElementById('displayData');
const tbody = document.getElementById('tbody');
let currentBookingId = null;
let currentDisplayedBooking = null;

function resetBookingForm() {
    currentBookingId = null;
    currentDisplayedBooking = null;
    formBooking.reset();
    bookingSubmit.value = 'Submit';
}

function populateBookingForm(booking) {
    document.getElementById('fname').value = booking.firstname;
    document.getElementById('lname').value = booking.lastname;
    document.getElementById('ci').value = booking.bookingdates.checkin;
    document.getElementById('co').value = booking.bookingdates.checkout;
    document.getElementById('an').value = booking.additionalneeds;
    currentBookingId = booking.id;
    currentDisplayedBooking = booking;
    bookingSubmit.value = 'Update Booking';
}

async function createBooking(data) {
    return axios.post("../controllers/booking.php", data, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
    });
}

async function updateBooking(id, data) {
    return axios.put("../controllers/booking.php", { id, ...data }, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
    });
}

async function deleteBooking(id) {
    return axios.delete("../controllers/booking.php", {
        params: { bookingID: id },
        headers: {
            "Accept": "application/json"
        }
    });
}

formBooking.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(formBooking);
    const data = {
        firstname: formData.get('fname'),
        lastname: formData.get('lname'),
        totalprice: 111,
        depositpaid: true,
        bookingdates: {
            checkin: formData.get('ci'),
            checkout: formData.get('co')
        },
        additionalneeds: formData.get('an')
    };

    try {
        const res = currentBookingId
            ? await updateBooking(currentBookingId, data)
            : await createBooking(data);

        if (res.data.success) {
            const action = currentBookingId ? 'updated' : 'saved';
            alert(`Booking ${action} with ID ${res.data.booking.id}`);
            displayTableData(res.data.booking);
            resetBookingForm();
        } else {
            alert(res.data.message || 'Booking failed');
        }
    } catch (err) {
        console.log(err);
        alert('Unable to save booking');
    }
});

async function getData(id) {
    try {
        const res = await axios.get(`../controllers/booking.php`, {
            params: { bookingID: id },
            headers: {
                "Accept": "application/json"
            }
        });

        if (res.data.success) {
            return res.data.booking;
        }

        alert(res.data.message || 'Booking not found');
        return null;
    } catch (err) {
        console.log(err);
        alert('Unable to fetch booking');
        return null;
    }
}

function displayTableData(data) {
    currentDisplayedBooking = data;
    tbody.innerHTML = '';

    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${data.firstname}</td>
        <td>${data.lastname}</td>
        <td>${data.totalprice}</td>
        <td>${data.depositpaid}</td>
        <td>${data.bookingdates.checkin}</td>
        <td>${data.bookingdates.checkout}</td>
        <td>${data.additionalneeds}</td>
        <td>
            <button class="delete-booking" data-id="${data.id}">Delete</button>
            <button class="edit-booking" data-id="${data.id}">Update</button>
        </td>
    `;
    tbody.appendChild(row);
}

tbody.addEventListener('click', async (e) => {
    const target = e.target;
    const bookingId = Number(target.dataset.id);
    if (!bookingId) return;

    if (target.classList.contains('delete-booking')) {
        if (!confirm('Delete this booking?')) return;
        try {
            const res = await deleteBooking(bookingId);
            if (res.data.success) {
                alert('Booking deleted');
                tbody.innerHTML = '';
                resetBookingForm();
            } else {
                alert(res.data.message || 'Delete failed');
            }
        } catch (err) {
            console.log(err);
            alert('Unable to delete booking');
        }
    }

    if (target.classList.contains('edit-booking')) {
        if (currentDisplayedBooking && currentDisplayedBooking.id === bookingId) {
            populateBookingForm(currentDisplayedBooking);
        } else {
            const booking = await getData(bookingId);
            if (booking) populateBookingForm(booking);
        }
    }
});

const displayForm = document.getElementById('displayData');
displayForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const bookingID = Number(document.getElementById('bookingID').value);
    if (!bookingID) {
        alert('Please enter a booking ID');
        return;
    }

    const data = await getData(bookingID);
    if (data) {
        displayTableData(data);
    }
});
