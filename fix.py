import pandas as pd
import statsmodels.api as sm
from flask import Flask, jsonify, request

# Membuat instance Flask
app = Flask(__name__)

# Path menuju file CSV yang berisi data wisatawan
file_path = 'Dataset/data_kota_fix.csv'

# Membaca data dari CSV dan mengonversi kolom 'Bulan' sebagai index
df = pd.read_csv(file_path, parse_dates=['Bulan'], index_col='Bulan')

# Menghapus spasi pada data dan mengubah kolom menjadi numerik
for column in df.columns:
    if column != 'Kota':  # Pastikan kolom kota tidak diubah menjadi numerik
        df[column] = df[column].replace({' ': '', ',': ''}, regex=True).astype(float)

# Parameter SARIMA (p, d, q)(P, D, Q, s) yang akan digunakan
order = (2, 1, 1)
seasonal_order = (1, 1, 1, 12)

# Fungsi untuk membangun model SARIMA dan melakukan prediksi
def sarima_forecast(series, order, seasonal_order, steps=12):
    try:
        # Pastikan series berupa numerik dan hapus data yang tidak valid (NaN)
        series = pd.to_numeric(series, errors='coerce').dropna()

        # Membangun model SARIMA langsung pada data asli
        model = sm.tsa.SARIMAX(series, order=order, seasonal_order=seasonal_order)
        results = model.fit(disp=False)

        # Melakukan prediksi untuk periode yang diminta
        forecast = results.get_forecast(steps=steps)
        forecasted_values = forecast.predicted_mean

        # Mengembalikan data historis dan prediksi dalam format JSON
        historical_data = series.to_json(orient='split')  # Data historis
        forecasted_data = forecasted_values.to_json(orient='split')  # Data prediksi

        return historical_data, forecasted_data
    except Exception as e:
        print(f"Error occurred for {series.name}: {e}")
        return None, None

# Membuat API untuk menghasilkan prediksi satu tahun ke depan beserta data historis berdasarkan kota
@app.route('/api/predictions', methods=['GET'])
def get_forecast():
    city = request.args.get('city')  # Dapatkan parameter kota dari request

    # Filter data berdasarkan kota
    city_data = df[df['Kota'] == city]

    if city_data.empty:
        return jsonify({'error': f'No data found for city {city}'}), 404

    forecasts = {}

    for country in city_data.columns:
        if country != 'Kota':  # Lewati kolom 'Kota' dalam prediksi
            historical_data, forecasted_data = sarima_forecast(city_data[country], order, seasonal_order, steps=12)
            if historical_data is not None:
                # Menyimpan data historis dan hasil prediksi dalam struktur dictionary untuk setiap negara
                forecasts[country] = {
                    'historical_data': historical_data,  # Data historis
                    'forecasted_data': forecasted_data  # Data prediksi untuk 12 bulan ke depan
                }

    return jsonify(forecasts)

# Menjalankan Flask API
if __name__ == '__main__':
    app.run(debug=True)
