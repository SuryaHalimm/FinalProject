## **Installation**

**----------------------**

### **Run from Source Code in Visual Studio Code**

1. Clone this repository:

   ```bash
   git clone https://github.com/SuryaHalimm/FinalProject.git

   ```
2. Create And Activate Virtual Environment:

   ```bash
   python -m venv venv
   .\venv\Scripts\activate

   ```

3. Install library dependencies:

   ```bash
   pip install -r requirements.txt

   ```

4. Run the application:

   ```bash
   python fix.py
   ```

### **Laravel Installation**

1. Install Composer Dependencies:

   ```bash
   composer install
   ```

2. Install Node.js Dependencies:

   ```bash
   npm install
   npm run dev
   ```

3. Copy the .env File: Copy the .env.example file to .env and update database configurations.

4. Configure Database in .env:

   ```bash
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. Run Migration and Seeder

   ```bash
   php artisan migrate --seed
   ```

6. Run Laravel Development Server

   ```bash
   php artisan serve
   ```

## **Manual Book**

**---------------------**

For detailed instructions on how to use this website, refer to the user manual available in this repository:  
[**Manual Book (PDF)**](https://github.com/SuryaHalimm/FinalProject/raw/main/535210020_ManualBook.pdf)

---