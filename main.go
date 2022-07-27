package main

import (
	"fmt"
	"math/rand"
	"sync"
	"time"
)

var onlyOnce sync.Once
var dice = []int{1, 2, 3, 4, 5, 6}

func acak() int {
	onlyOnce.Do(func() {
		rand.Seed(time.Now().UnixNano())
	})

	return dice[rand.Intn(len(dice))]
}

func PermainanDadu(total int, n int) map[int]int {
	var koleksi = make(map[int][]int)
	var points = make(map[int]int)

	// Cek Jumlah Pemain
	if total < 2 {
		fmt.Println("Jumlah Pemain minimal 2")
		return nil
	}

	// Pemain Melempar Dadu
	for pemain := 1; pemain <= total; pemain++ {
		for dadu := 1; dadu <= n; dadu++ {
			hasil := acak()
			koleksi[pemain] = append(koleksi[pemain], hasil)
		}
	}

	fmt.Println("Jumlah pemain :", len(koleksi))
	fmt.Println("Isi dadu pemain :", koleksi)

	// Evaluasi Hasil Dadu
	for len(koleksi) > 1 {
		for i, val := range koleksi {
			fmt.Println("Pemain ke :", i, "Panjang Dadu :", len(koleksi[i]))

			for j := 0; j < len(val); j++ {
				if val[j] == 6 {
					points[i]++
					fmt.Println("index yang akan dihapus :", j)
					koleksi[i] = remove(koleksi[i], j)
					fmt.Println("Eliminasi pemain ke :", i, "koleksi == 6 :", koleksi)

					// break
					if len(koleksi[i]) == 0 {
						fmt.Println("Hapus pemain :", i)
						delete(koleksi, i)
						if len(koleksi) == 1 {
							return points
						}
					}
				} else if val[j] == 1 {
					fmt.Println("index yang akan dihapus :", j)
					fmt.Println("index terakhir == :", len(val)-1)

					if j == len(val)-1 {
						koleksi[1] = append(koleksi[1], 1)
					} else {
						koleksi[i+1] = append(koleksi[i+1], 1)
					}

					// stuck at here jika angka 1 pemberian pemain sebelumnya
					koleksi[i] = remove(koleksi[i], j)
					fmt.Println("Eliminasi pemain ke :", i, "koleksi == 1 :", koleksi)

					// break
					if len(koleksi[i]) == 0 {
						fmt.Println("Hapus pemain :", i)
						delete(koleksi, i)
						if len(koleksi) == 1 {
							return points
						}
					}
				} else if val[j] == 2 || val[j] == 3 || val[j] == 4 || val[j] == 5 {
					fmt.Println("Eliminasi pemain ke :", i, "koleksi Selain 6 dan 1 :", koleksi)

					// break
				}
			}

			// Stuck at here
			// Jika pakai break stuck di Looping dadu selanjutnya
			// Jika tanpa break stuck di remove angka 1
			// (seharusnya tidak boleh dihapus karena diberikan dari pemain sebelumnya)

			if len(koleksi[i]) == 0 {
				fmt.Println("Hapus pemain :", i)
				delete(koleksi, i)
				if len(koleksi) == 1 {
					return points
				}
			}
		}

		fmt.Println("Lempar Dadu Lagi")
		fmt.Println("Jumlah pemain :", len(koleksi))
		fmt.Println("Panjang Dadu :", koleksi)

		// Pemain Melempar Dadu
		for pemain := 1; pemain <= len(koleksi); pemain++ {
			for dadu := 1; dadu <= len(koleksi[pemain]); dadu++ {
				hasil := acak()
				koleksi[pemain] = append(koleksi[pemain], hasil)
			}
		}

	}

	fmt.Println("Akhir koleksi :", koleksi)
	fmt.Println("Total points :", points)

	return points
}

func remove(slice []int, index int) []int {
	return append(slice[:index], slice[index+1:]...)
}

func main() {
	points := PermainanDadu(2, 2)

	// Menampilkan Hasil Permainan
	for i, val := range points {
		pemain := 0
		max := 0
		if val > max {
			pemain = i
			max = val
		}

		fmt.Println("Pemenang = Pemain ke :", pemain, "Total points :", max)
	}
}
