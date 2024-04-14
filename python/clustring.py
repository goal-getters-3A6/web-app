import json
from collections import Counter
import matplotlib.pyplot as plt
import pandas as pd
from sqlalchemy import create_engine

def fetch_reservation_data_from_database():
    # Create an SQLAlchemy engine to connect to the database
    DATABASE_URL = "mysql://root@127.0.0.1:3306/pidev"
    engine = create_engine(DATABASE_URL)
    
    # Query to fetch reservation data from the database
    query = """
    SELECT s.nom, COUNT(r.idreservation) AS reservation_count
    FROM Seance s
    LEFT JOIN reservation r ON s.idseance = r.ids
    GROUP BY s.nom
    """
    
    # Execute the query and fetch the results into a DataFrame
    df = pd.read_sql_query(query, engine)
    
    return df

def plot_reservation_statistics(reservation_data):
    # Plotting the bar chart
    plt.figure(figsize=(10, 6))
    plt.bar(reservation_data['nom'], reservation_data['reservation_count'], color='skyblue')
    plt.xlabel('Nom de la Séance')
    plt.ylabel('Nombre de Réservations')
    plt.title('Statistiques des Réservations par Séance')
    plt.xticks(rotation=45, ha='right')
    plt.tight_layout()
    plt.show()

# Fetch reservation data from the database
reservation_data_from_database = fetch_reservation_data_from_database()

# Plot reservation statistics
plot_reservation_statistics(reservation_data_from_database)
