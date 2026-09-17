/* Property list app - React component (PG Life)
 * Renders the PG listing for a city, with city/budget/gender/sort
 * filtering done via AJAX (no page reload) and interested-toggle
 * handled through the toggle_interested API.
 */

/*jshint esversion: 6, -W097 */
const React = window.React;
const ReactDOM = window.ReactDOM;
const { useState, useEffect } = React;

const API_URL = window.PG_LIFE && window.PG_LIFE.apiUrl ? window.PG_LIFE.apiUrl : "api/filter_properties.php";
const INIT_CITY = window.PG_LIFE && window.PG_LIFE.city ? window.PG_LIFE.city : "";

function formatRent(rent) {
    return Number(rent).toLocaleString("en-IN");
}

/* Read the per-session CSRF token emitted in the page <head>. */
function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
}

function Rating({ value }) {
    const stars = [];
    for (let i = 0; i < 5; i++) {
        if (value >= i + 0.8) {
            stars.push(<i key={"s" + i} className="fas fa-star"></i>);
        } else if (value >= i + 0.3) {
            stars.push(<i key={"s" + i} className="fas fa-star-half-alt"></i>);
        } else {
            stars.push(<i key={"s" + i} className="far fa-star"></i>);
        }
    }
    return <div className="star-container" title={"Rating: " + value}>{stars}</div>;
}

function PropertyCard({ property, onToggleInterested }) {
    const genderImage =
        property.gender === "male"
            ? "img/male.png"
            : property.gender === "female"
            ? "img/female.png"
            : "img/unisex.png";
    const heartClass =
        "interested-btn__react " + (property.is_interested ? "fas" : "far") + " fa-heart";
    const detailUrl = "property_detail.php?property_id=" + property.id;

    return (
        <div className="property-card row">
            <div className="image-container col-md-4">
                <a href={detailUrl}>
                    {property.image ? (
                        <img src={property.image} alt={property.name} className="d-block m-auto" />
                    ) : (
                        <div style={{ padding: "40px" }}>
                            <i className="fas fa-image" style={{ color: "#d0d0d0", fontSize: "48px" }}></i>
                        </div>
                    )}
                </a>
            </div>
            <div className="content-container col-md-8">
                <div className="row no-gutters justify-content-between">
                    <Rating value={property.rating || 0} />
                    <div className="interested-container">
                        <i
                            className={heartClass}
                            property_id={property.id}
                            onClick={(e) => onToggleInterested(property, e)}
                        ></i>
                        <div className="interested-text">
                            <span className="interested-user-count">{property.interested_count}</span> interested
                        </div>
                    </div>
                </div>
                <div className="detail-container">
                    <div className="property-name">{property.name}</div>
                    <div className="property-address">{property.address}</div>
                    <div className="property-gender">
                        <img src={genderImage} alt={property.gender} />
                    </div>
                </div>
                <div className="row no-gutters">
                    <div className="rent-container col-6">
                        <div className="rent">₹ {formatRent(property.rent)}/-</div>
                        <div className="rent-unit">per month</div>
                    </div>
                    <div className="button-container col-6">
                        <a href={detailUrl} className="btn btn-primary">
                            View
                        </a>
                    </div>
                </div>
            </div>
        </div>
    );
}

function FilterOption({ active, onClick, children }) {
    return (
        <button
            type="button"
            className={"filter-option" + (active ? " active" : "")}
            onClick={onClick}
        >
            {children}
        </button>
    );
}

function PropertyListApp() {
    const [gender, setGender] = useState("");
    const [sort, setSort] = useState("");
    const [minRent, setMinRent] = useState("");
    const [maxRent, setMaxRent] = useState("");

    const [properties, setProperties] = useState([]);
    const [count, setCount] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");

    useEffect(() => {
        const timer = setTimeout(() => {
            setLoading(true);
            setError("");

            const params = new URLSearchParams();
            params.set("city", INIT_CITY);
            if (gender) params.set("gender", gender);
            if (minRent && !isNaN(minRent)) params.set("min_rent", minRent);
            if (maxRent && !isNaN(maxRent)) params.set("max_rent", maxRent);
            if (sort) params.set("sort", sort);

            fetch(API_URL + "?" + params.toString())
                .then((res) => res.json())
                .then((json) => {
                    if (json.success) {
                        setProperties(json.properties || []);
                        setCount(json.count || 0);
                        if (json.city && json.city.name) {
                            const crumb = document.getElementById("city-breadcrumb");
                            if (crumb) crumb.textContent = json.city.name;
                            document.title = "Best PG's in " + json.city.name + " | PG Life";
                        }
                    } else {
                        setProperties([]);
                        setCount(0);
                        setError(json.message || "Something went wrong!");
                    }
                })
                .catch(() => {
                    setProperties([]);
                    setCount(0);
                    setError("Something went wrong!");
                })
                .finally(() => setLoading(false));
        }, 350);

        return () => clearTimeout(timer);
    }, [gender, minRent, maxRent, sort]);

    function toggleInterested(property, e) {
        e.preventDefault();
        showLoading();
        fetch("api/toggle_interested.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "property_id=" + encodeURIComponent(property.id) + "&csrf_token=" + encodeURIComponent(csrfToken())
        })
            .then((res) => res.json())
            .then((json) => {
                hideLoading();
                if (json.success) {
                    setProperties((prev) =>
                        prev.map((p) =>
                            p.id === property.id
                                ? {
                                      ...p,
                                      is_interested: json.interested,
                                      interested_count: Math.max(
                                          0,
                                          p.interested_count + (json.interested ? 1 : -1)
                                      )
                                  }
                                : p
                        )
                    );
                } else if (json.message && json.message.toLowerCase().indexOf("login") > -1) {
                    if (window.jQuery && window.jQuery("#login-modal").length) {
                        window.jQuery("#login-modal").modal("show");
                    } else {
                        alert(json.message);
                    }
                } else {
                    alert(json.message || "Something went wrong!");
                }
            })
            .catch(() => {
                hideLoading();
                alert("Something went wrong!");
            });
    }

    const genderButtons = [
        { key: "", label: "All" },
        { key: "male", label: "Male" },
        { key: "female", label: "Female" },
        { key: "unisex", label: "Unisex" }
    ];

    const sortButtons = [
        { key: "", label: "Default" },
        { key: "rent_asc", label: "Lowest rent first" },
        { key: "rent_desc", label: "Highest rent first" }
    ];

    return (
        <div>
            <div className="filter-bar row justify-content-around align-items-center">
                <div className="col-auto">
                    <b>Gender:</b>
                    <br />
                    {genderButtons.map((g) => (
                        <FilterOption
                            key={g.key}
                            active={gender === g.key}
                            onClick={() => setGender(g.key)}
                        >
                            {g.key ? (
                                <img
                                    src={"img/" + g.key + ".png"}
                                    alt={g.label}
                                    style={{ width: "22px", marginRight: "4px" }}
                                />
                            ) : null}
                            {g.label}
                        </FilterOption>
                    ))}
                </div>
                <div className="col-auto">
                    <b>Budget:</b>
                    <br />
                    <span>
                        ₹
                        <input
                            type="number"
                            min="0"
                            className="budget-input"
                            placeholder="Min"
                            value={minRent}
                            onChange={(e) => setMinRent(e.target.value)}
                        />
                        – ₹
                        <input
                            type="number"
                            min="0"
                            className="budget-input"
                            placeholder="Max"
                            value={maxRent}
                            onChange={(e) => setMaxRent(e.target.value)}
                        />
                    </span>
                </div>
                <div className="col-auto">
                    <b>Sort:</b>
                    <br />
                    {sortButtons.map((s) => (
                        <FilterOption
                            key={s.key}
                            active={sort === s.key}
                            onClick={() => setSort(s.key)}
                        >
                            {s.label}
                        </FilterOption>
                    ))}
                </div>
            </div>

            {loading ? (
                <div className="ajax-spinner">
                    <img src="img/progress_spinner.gif" alt="Loading" />
                </div>
            ) : error ? (
                <div className="no-property-container">
                    <p>{error}</p>
                </div>
            ) : count === 0 ? (
                <div className="no-property-container">
                    <p>OOPS! No matching PG found in {INIT_CITY}.</p>
                </div>
            ) : (
                <div>
                    <div className="listing-count">
                        Showing {count} PG&apos;s in {INIT_CITY}
                    </div>
                    {properties.map((p) => (
                        <PropertyCard
                            key={p.id}
                            property={p}
                            onToggleInterested={toggleInterested}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}

ReactDOM.createRoot(document.getElementById("property-list-app")).render(
    <PropertyListApp />
);