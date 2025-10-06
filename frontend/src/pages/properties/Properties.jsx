import React, { useEffect, useState } from "react";
import { crudApi } from "../../api/crudApi";

const Properties = () => {
  const [properties, setProperties] = useState([]);
  const [form, setForm] = useState({
    property_type: "",
    project_name: "",
    asking_price: "",
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const fetchProperties = async () => {
    try {
      setLoading(true);
      const response = await crudApi.getAllProperties();
      // if backend returns pagination: response.data.data
      setProperties(response.data.data || response.data || []);
    } catch (err) {
      console.error(err);
      setError("Failed to load properties.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchProperties();
  }, []);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError(null);

    try {
      await crudApi.createProperty(form);
      alert("✅ Property added successfully!");
      setForm({ property_type: "", project_name: "", asking_price: "" });
      fetchProperties();
    } catch (err) {
      console.error(err);
      setError(
        err?.response?.data?.message ||
          err?.response?.data?.error ||
          "Failed to add property."
      );
    }
  };

  return (
    <div className="p-8">
      <h1 className="text-2xl font-bold mb-6">🏠 Property List</h1>

      <form onSubmit={handleSubmit} className="bg-white p-4 rounded shadow-md mb-8 w-full md:w-1/2">
        <h2 className="text-lg font-semibold mb-4">Add New Property</h2>
        <div className="space-y-3">
          <input
            type="text"
            name="property_type"
            value={form.property_type}
            onChange={handleChange}
            placeholder="Property Type (e.g. Apartment)"
            className="w-full border p-2 rounded"
            required
          />
          <input
            type="text"
            name="project_name"
            value={form.project_name}
            onChange={handleChange}
            placeholder="Project Name"
            className="w-full border p-2 rounded"
          />
          <input
            type="number"
            name="asking_price"
            value={form.asking_price}
            onChange={handleChange}
            placeholder="Asking Price"
            className="w-full border p-2 rounded"
          />
          <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Add Property
          </button>
        </div>
      </form>

      {loading ? (
        <p>Loading properties...</p>
      ) : error ? (
        <p className="text-red-500">{error}</p>
      ) : properties.length === 0 ? (
        <p>No properties found.</p>
      ) : (
        <table className="min-w-full bg-white border">
          <thead>
            <tr className="bg-gray-100 border-b">
              <th className="p-2 text-left">#</th>
              <th className="p-2 text-left">Type</th>
              <th className="p-2 text-left">Project Name</th>
              <th className="p-2 text-left">Asking Price</th>
            </tr>
          </thead>
          <tbody>
            {properties.map((p, i) => (
              <tr key={p.id} className="border-b hover:bg-gray-50">
                <td className="p-2">{i + 1}</td>
                <td className="p-2">{p.property_type}</td>
                <td className="p-2">{p.project_name}</td>
                <td className="p-2">{p.asking_price}</td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </div>
  );
};

export default Properties;
