package controllers

import (
	"encoding/json"
	"log"
	"net/http"
	"os"

	"github.com/gorilla/mux"
	"gitlab.od.atncorp.com/web/promos/app/common"
	"gitlab.od.atncorp.com/web/promos/app/models"
)

func CreateCRUDHandler(w http.ResponseWriter, r *http.Request) {

	scope := []string{"admin", "sales"}
	if err := authenticate(r, scope); err != nil {
		common.DisplayAppError(w, err, 401)
		return
	}
	var err error
	//parametr witch module is calling
	module := r.URL.Query().Get("module")

	if module == "" {
		common.DisplayAppError(w, err, 400)
		return
	}

	client, err := common.GetClient(r.Context())
	if err != nil {
		common.DisplayAppError(w, err, 400)
		return
	}

	if module == "specials" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLSPESIALS)
		var data models.Special
		if err := parseRequest(r, &data); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}
		repo := &models.SpecialsRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		if common.Debug {
			log.Println("["+r.Method+"]", r.URL, "CreateSpecialHandler", data)
		}

		if err := data.Validate(); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		if _, err := repo.Create(&data); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		mes := ResponseMessage{
			HTTPStatus: 200,
			Message:    "Special " + data.Title + " created.",
		}

		j, err := json.Marshal(mes)
		if err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	} else if module == "upgrades" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLUPGRADES)
		var data models.Upgrade
		if err := parseRequest(r, &data); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}
		repo := &models.UpgradesRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		if common.Debug {
			log.Println("["+r.Method+"]", r.URL, "CreateUpgradeHandler", data)
		}

		if err := data.Validate(); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		if _, err := repo.Create(&data); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		mes := ResponseMessage{
			HTTPStatus: 200,
			Message:    "Upgrade " + data.Title + " created.",
		}

		j, err := json.Marshal(mes)
		if err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	}
}

func CRUDInfoByIDHandler(w http.ResponseWriter, r *http.Request) {

	scope := []string{"admin", "sales"}
	if err := authenticate(r, scope); err != nil {
		common.DisplayAppError(w, err, 401)
		return
	}
	vars := mux.Vars(r)

	var err error

	client, err := common.GetClient(r.Context())
	if err != nil {
		common.DisplayAppError(w, err, 400)
		return
	}

	//parametr witch module is calling
	module := r.URL.Query().Get("module")

	if module == "" {
		common.DisplayAppError(w, err, 400)
		return
	}
	if module == "specials" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLSPESIALS)
		repo := &models.SpecialsRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		data, err := repo.GetSpecialByID(vars["id"])

		if err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		j, err := json.Marshal(data)
		if err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	} else if module == "upgrades" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLUPGRADES)
		repo := &models.UpgradesRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		data, err := repo.GetUpgradeByID(vars["id"])

		if err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		j, err := json.Marshal(data)
		if err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	}
}

func GetAllCRUDHandler(w http.ResponseWriter, r *http.Request) {

	scope := []string{"admin", "sales"}
	if err := authenticate(r, scope); err != nil {
		common.DisplayAppError(w, err, 401)
		return
	}

	var err error

	client, err := common.GetClient(r.Context())
	if err != nil {
		common.DisplayAppError(w, err, 400)
		return
	}

	//parametr witch module is calling
	module := r.URL.Query().Get("module")

	if module == "" {
		common.DisplayAppError(w, err, 400)
		return
	}
	if module == "specials" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLSPESIALS)

		repo := &models.SpecialsRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		data, err := repo.GetAll()
		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}

		j, err := json.Marshal(data)
		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	} else if module == "upgrades" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLUPGRADES)

		repo := &models.UpgradesRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		data, err := repo.GetAll()
		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}

		j, err := json.Marshal(data)
		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	}
}

func UpdateCRUDHandler(w http.ResponseWriter, r *http.Request) {

	scope := []string{"admin", "sales"}
	var err error
	if err := authenticate(r, scope); err != nil {
		common.DisplayAppError(w, err, 401)
		return
	}
	//parametr witch module is calling
	module := r.URL.Query().Get("module")

	if module == "" {
		common.DisplayAppError(w, err, 400)
		return
	}

	client, err := common.GetClient(r.Context())
	if err != nil {
		common.DisplayAppError(w, err, 400)
		return
	}

	if module == "specials" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLSPESIALS)
		var data models.Special
		if err := parseRequest(r, &data); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}
		repo := &models.SpecialsRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		if common.Debug {
			log.Println("["+r.Method+"]", r.URL, "UpdateSpecialHandler", data)
		}
		_, err = repo.GetSpecialByID(data.ID.Hex())

		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}
		if err := data.Validate(); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}
		if _, err := repo.Update(&data); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		mes := ResponseMessage{
			HTTPStatus: 200,
			Message:    "Special " + data.Title + " updated.",
		}

		j, err := json.Marshal(mes)
		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	} else if module == "upgrades" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLUPGRADES)
		var data models.Upgrade
		if err := parseRequest(r, &data); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}
		repo := &models.UpgradesRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		if common.Debug {
			log.Println("["+r.Method+"]", r.URL, "UpdateUpgradeHandler", data)
		}
		_, err = repo.GetUpgradeByID(data.ID.Hex())

		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}
		if err := data.Validate(); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}
		if _, err := repo.Update(&data); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		mes := ResponseMessage{
			HTTPStatus: 200,
			Message:    "Upgrade " + data.Title + " updated.",
		}

		j, err := json.Marshal(mes)
		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	}
}

func DeleteCRUDlHandler(w http.ResponseWriter, r *http.Request) {

	scope := []string{"admin", "sales"}
	if err := authenticate(r, scope); err != nil {
		common.DisplayAppError(w, err, 401)
		return
	}
	vars := mux.Vars(r)

	var err error

	client, err := common.GetClient(r.Context())
	if err != nil {
		common.DisplayAppError(w, err, 400)
		return
	}

	//parametr witch module is calling
	module := r.URL.Query().Get("module")

	if module == "" {
		common.DisplayAppError(w, err, 400)
		return
	}

	if module == "specials" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLSPESIALS)

		repo := &models.SpecialsRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		if common.Debug {
			log.Println("["+r.Method+"]", r.URL, "DeleteSpecialHandler")
		}
		_, err = repo.GetSpecialByID(vars["id"])

		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}

		if _, err := repo.Delete(vars["id"]); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		mes := ResponseMessage{
			HTTPStatus: 200,
			Message:    "Special deleted.",
		}

		j, err := json.Marshal(mes)
		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	} else if module == "upgrades" {
		c := client.Database(os.Getenv("DBNAME")).Collection(common.COLUPGRADES)

		repo := &models.UpgradesRepository{Ctx: r.Context(), C: c}
		defer common.CloseClient(r.Context(), client)
		if common.Debug {
			log.Println("["+r.Method+"]", r.URL, "DeleteUpgradeHandler")
		}
		_, err = repo.GetUpgradeByID(vars["id"])

		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}

		if _, err := repo.Delete(vars["id"]); err != nil {
			common.DisplayAppError(w, err, 400)
			return
		}

		mes := ResponseMessage{
			HTTPStatus: 200,
			Message:    "Upgrade deleted.",
		}

		j, err := json.Marshal(mes)
		if err != nil {
			common.DisplayAppError(w, err, 500)
			return
		}
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(j)
	}
}
