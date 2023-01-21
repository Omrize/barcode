class App{

    constructor(options){
        this.generateBtn = document.querySelector('#generate');
        this.messageElement = document.querySelector('#message');
        this.qrContainer = document.querySelector('#qr-container');
        this.overlay = document.querySelector('#overlay');
        this.error = document.querySelector('#error');
        this.errorMessage = error.querySelector('#error-message');
        this.typeElement = document.querySelector('#type');
    }

    init(){
        this.messageElement.addEventListener('keyup', () => { this.fetchBarcode() });
        this.typeElement.addEventListener('change', () => { this.fetchBarcode() });
    }
    
    fetchBarcode(){
        const data = {
            text: this.messageElement.value,
            type: this.typeElement.value,
            is2D: !!this.typeElement.options[this.typeElement.selectedIndex].getAttribute('data-2d') ?? false
        };

        if(data.text === '' || data.type === '') return;

        this.overlay.classList.remove('hidden');
        this.error.classList.add('hidden');

        
        fetch('./qrcode-generator.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(data => {
            this.overlay.classList.add('hidden');

            if(data.Result != 'OK'){
                this.errorMessage.innerText = data.error;
                this.error.classList.remove('hidden');
            }else{
                this.qrContainer.innerHTML =  data.data;
            }
        })
    }
}

const app = new App();
app.init();