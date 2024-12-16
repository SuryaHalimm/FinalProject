import pandas as pd
import statsmodels.api as sm
from flask import Flask, jsonify, request

# Membuat instance Flask
app = Flask(__name__)

# Path menuju file CSV yang berisi data wisatawan dan file parameter yang dituning
file_path = 'data_kota_fix.csv'
tuned_params_path = 'hasil_tuning.xlsx'

# Membaca data wisatawan dan parameter yang dituning
df = pd.read_csv(file_path, parse_dates=['Bulan'], index_col='Bulan')
tuned_params_df = pd.read_excel(tuned_params_path)

# Membersihkan data
for column in df.columns:
    if column != 'Kota':
        df[column] = df[column].replace({' ': '', ',': ''}, regex=True).astype(float)

# Fungsi untuk membangun model SARIMA dan melakukan prediksi
def sarima_forecast(series, order, seasonal_order, steps=12):
    try:
        series = pd.to_numeric(series, errors='coerce').dropna()
        model = sm.tsa.SARIMAX(series, order=order, seasonal_order=seasonal_order, 
                               enforce_stationarity=False, enforce_invertibility=False)
        results = model.fit(disp=False)
        forecast = results.get_forecast(steps=steps)
        forecasted_values = forecast.predicted_mean
        historical_data = series.to_json(orient='split')
        forecasted_data = forecasted_values.to_json(orient='split')
        return historical_data, forecasted_data
    except Exception as e:
        print(f"Error occurred for {series.name}: {e}")
        return None, None

# Membuat API untuk menghasilkan prediksi berdasarkan kota (semua kebangsaan)
@app.route('/api/predictions', methods=['GET'])
def get_forecast():
    # Mendapatkan parameter kota dari permintaan
    city = request.args.get('city')

    # Filter data berdasarkan kota
    city_data = df[df['Kota'] == city]

    if city_data.empty:
        return jsonify({'error': f'Tidak ada data yang ditemukan untuk kota {city}'}), 404

    # Menyimpan hasil prediksi untuk setiap kebangsaan
    predictions = {}

    # Loop melalui semua kebangsaan dalam data kota
    for country in city_data.columns:
        if country != 'Kota':  # Lewati kolom 'Kota'
            # Cari parameter terbaik untuk kota dan kebangsaan
            params = tuned_params_df[(tuned_params_df['Kota'] == city) & (tuned_params_df['Kebangsaan'] == country)]

            if params.empty:
                print(f"Tidak ada parameter tuning untuk kota {city} dan kebangsaan {country}")
                continue

            # Mengambil parameter terbaik
            best_order = eval(params.iloc[0]['Best Order'])
            best_seasonal_order = eval(params.iloc[0]['Best Seasonal Order'])

            # Melakukan prediksi menggunakan parameter terbaik
            series = city_data[country]
            historical_data, forecasted_data = sarima_forecast(series, best_order, best_seasonal_order, steps=12)

            if historical_data and forecasted_data:
                predictions[country] = {
                    'historical_data': historical_data,
                    'forecasted_data': forecasted_data
                }

    if not predictions:
        return jsonify({'error': f'Gagal menghasilkan prediksi untuk kota {city}'}), 500

    return jsonify(predictions)

# Menjalankan Flask API
if __name__ == '__main__':
    app.run(debug=True)
